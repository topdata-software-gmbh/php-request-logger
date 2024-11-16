<?php

// Include this in your index.php
class RequestLogger
{
    private $logDir;

    public function __construct($baseLogDir = '/var/log/http-requests')
    {
        $this->logDir = rtrim($baseLogDir, '/');
        $this->ensureDirectoryExists();
    }

    private function ensureDirectoryExists()
    {
        $todayDir = $this->getTodayDirectory();
        if (!is_dir($todayDir)) {
            mkdir($todayDir, 0755, true);
        }
    }

    private function getTodayDirectory()
    {
        return $this->logDir . '/' . date('Y-m-d');
    }

    public function logRequest()
    {
        $request = [
            'timestamp'    => date('Y-m-d H:i:s'),
            'ip'           => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'method'       => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'uri'          => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'referer'      => $_SERVER['HTTP_REFERER'] ?? '',
            'query_string' => $_SERVER['QUERY_STRING'] ?? '',
            'protocol'     => $_SERVER['SERVER_PROTOCOL'] ?? 'unknown',
            'headers'      => getallheaders(),
            'post_data'    => $this->sanitizePostData($_POST),
        ];

        $logFile = $this->getTodayDirectory() . '/requests.jsonl';
        file_put_contents(
            $logFile,
            json_encode($request, JSON_UNESCAPED_SLASHES) . "\n",
            FILE_APPEND
        );
    }

    private function sanitizePostData($data)
    {
        // Remove sensitive data (passwords, tokens, etc.)
        $sensitiveKeys = ['password', 'token', 'key', 'secret'];
        return array_filter(
            $data,
            function ($key) use ($sensitiveKeys) {
                return !in_array(strtolower($key), $sensitiveKeys);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

// Example usage:

// In your index.php:
// require_once 'logger.php';
// $logger = new RequestLogger('/path/to/logs');
// $logger->logRequest();

