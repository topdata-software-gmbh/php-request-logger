<?php

// analyzer.php - Script to analyze logs
class LogAnalyzer
{
    private $logDir;

    public function __construct($logDir)
    {
        $this->logDir = rtrim($logDir, '/');
    }

    public function analyze($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $endDate ?? date('Y-m-d');

        $stats = [
            'total_requests'      => 0,
            'methods'             => [],
            'uris'                => [],
            'status_codes'        => [],
            'user_agents'         => [],
            'ips'                 => [],
            'hourly_distribution' => array_fill(0, 24, 0),
            'referers'            => [],
        ];

        $current = new DateTime($startDate);
        $end = new DateTime($endDate);

        while ($current <= $end) {
            $dayDir = $this->logDir . '/' . $current->format('Y-m-d');
            $logFile = $dayDir . '/requests.jsonl';

            if (file_exists($logFile)) {
                $handle = fopen($logFile, 'r');
                while (($line = fgets($handle)) !== false) {
                    $request = json_decode($line, true);
                    $this->processRequest($request, $stats);
                }
                fclose($handle);
            }

            $current->modify('+1 day');
        }

        $this->calculatePercentages($stats);
        return $stats;
    }

    private function processRequest($request, &$stats)
    {
        $stats['total_requests']++;

        // Count methods
        $stats['methods'][$request['method']] =
            ($stats['methods'][$request['method']] ?? 0) + 1;

        // Count URIs
        $stats['uris'][$request['uri']] =
            ($stats['uris'][$request['uri']] ?? 0) + 1;

        // Count User Agents
        $stats['user_agents'][$request['user_agent']] =
            ($stats['user_agents'][$request['user_agent']] ?? 0) + 1;

        // Count IPs
        $stats['ips'][$request['ip']] =
            ($stats['ips'][$request['ip']] ?? 0) + 1;

        // Count by hour
        $hour = (int)substr($request['timestamp'], 11, 2);
        $stats['hourly_distribution'][$hour]++;

        // Count referers
        if (!empty($request['referer'])) {
            $stats['referers'][$request['referer']] =
                ($stats['referers'][$request['referer']] ?? 0) + 1;
        }
    }

    private function calculatePercentages(&$stats)
    {
        foreach (['methods', 'uris', 'user_agents', 'ips', 'referers'] as $key) {
            arsort($stats[$key]);
            $stats[$key . '_top_10'] = array_slice($stats[$key], 0, 10, true);
        }

        $stats['hourly_distribution_percentage'] = array_map(
            function ($count) use ($stats) {
                return round(($count / $stats['total_requests']) * 100, 2);
            },
            $stats['hourly_distribution']
        );
    }
}

// Example usage:

// To analyze logs:
// require_once 'analyzer.php';
// $analyzer = new LogAnalyzer('/path/to/logs');
// $stats = $analyzer->analyze('2024-01-01', '2024-01-31');
// print_r($stats);
