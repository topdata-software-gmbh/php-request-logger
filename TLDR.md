# HTTP Request Logger & Analyzer

A PHP-based system for logging and analyzing HTTP requests.

## Key Features
- Logs HTTP requests with sanitized data (removes sensitive information)
- Organizes logs by date in separate directories
- Analyzes request patterns and generates statistics
- Handles sensitive data safely by filtering out passwords, tokens, etc.

## Usage Examples

### Logging Requests

```php
// Initialize the logger
require_once 'RequestLogger.php';
$logger = new RequestLogger('/var/log/http-requests');

// In your request handling code:
$logger->logRequest([
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'ip' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
    'post_data' => $_POST  // Will be automatically sanitized
]);
```

### Analyzing Logs

```php
// Initialize the analyzer
require_once 'LogAnalyzer.php';
$analyzer = new LogAnalyzer('/var/log/http-requests');

// Get statistics for last month
$startDate = date('Y-m-d', strtotime('-1 month'));
$endDate = date('Y-m-d');
$stats = $analyzer->analyze($startDate, $endDate);

// Print the results
echo "Total Requests: {$stats['total_requests']}\n";
echo "\nTop IP Addresses:\n";
foreach ($stats['top_ips'] as $ip => $count) {
    echo "$ip: $count requests\n";
}
echo "\nMost Common URIs:\n";
foreach ($stats['top_uris'] as $uri => $count) {
    echo "$uri: $count hits\n";
}
```

## What You Get
- Total request counts
- Top 10 most frequent:
  - HTTP methods
  - URIs
  - User agents
  - IP addresses
  - Referrers
- Hourly request distribution
