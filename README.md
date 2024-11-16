# HTTP Request Logger and Analyzer

A lightweight PHP library for logging and analyzing HTTP requests. This project provides two main components:
1. A request logger that captures detailed HTTP request data in JSONL format
2. An analyzer that generates comprehensive statistics from the logged data

## Features

### Request Logger
- Automatic daily log rotation (one directory per day)
- JSONL format for easy parsing and analysis
- Comprehensive request data capturing:
  - Timestamp
  - IP address
  - HTTP method
  - URI
  - User agent
  - Referer
  - Query string
  - Headers
  - POST data (with sensitive data filtering)
- Automatic log directory creation
- Sensitive data filtering for POST requests

### Log Analyzer
- Date range analysis
- Comprehensive statistics including:
  - Total request count
  - HTTP method distribution
  - Most requested URIs
  - User agent distribution
  - IP address frequency
  - Hourly request distribution
  - Top referers
  - Percentages for all metrics
  - Top 10 lists for key metrics

## Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/http-request-logger.git
cd http-request-logger
```

2. Ensure your log directory is writable by your web server:
```bash
mkdir -p /var/log/http-requests
chown www-data:www-data /var/log/http-requests  # Adjust user/group as needed
chmod 755 /var/log/http-requests
```

## Usage

### Setting Up Request Logging

1. Include the logger in your `index.php`:

```php
require_once 'logger.php';

// Initialize the logger with your preferred log directory
$logger = new RequestLogger('/var/log/http-requests');

// Log the current request
$logger->logRequest();

// Your existing index.php code continues here...
```

### Analyzing Logs

```php
require_once 'analyzer.php';

// Initialize the analyzer
$analyzer = new LogAnalyzer('/var/log/http-requests');

// Analyze last 7 days (default)
$stats = $analyzer->analyze();

// Or analyze a specific date range
$stats = $analyzer->analyze('2024-01-01', '2024-01-31');

// Print the statistics
print_r($stats);
```

### Sample Output

The analyzer returns an array with the following structure:

```php
[
    'total_requests' => 1234,
    'methods' => [
        'GET' => 1000,
        'POST' => 200,
        // ...
    ],
    'methods_top_10' => [...],
    'uris' => [
        '/api/v1/users' => 500,
        '/home' => 300,
        // ...
    ],
    'uris_top_10' => [...],
    'user_agents' => [...],
    'user_agents_top_10' => [...],
    'ips' => [...],
    'ips_top_10' => [...],
    'hourly_distribution' => [
        0 => 50,  // requests at hour 0
        1 => 45,  // requests at hour 1
        // ...
    ],
    'hourly_distribution_percentage' => [
        0 => 4.05,  // percentage of requests at hour 0
        1 => 3.65,  // percentage of requests at hour 1
        // ...
    ],
    'referers' => [...],
    'referers_top_10' => [...]
]
```

## Log Format

Each log entry is stored as a single line of JSON with the following structure:

```json
{
    "timestamp": "2024-01-01 12:34:56",
    "ip": "192.168.1.1",
    "method": "GET",
    "uri": "/path/to/resource",
    "user_agent": "Mozilla/5.0 ...",
    "referer": "https://example.com",
    "query_string": "param1=value1&param2=value2",
    "protocol": "HTTP/1.1",
    "headers": {
        "Host": "example.com",
        "Accept": "text/html,application/xhtml+xml...",
        // ...
    },
    "post_data": {
        // POST data with sensitive information filtered
    }
}
```

## Security Considerations

- Sensitive POST data (passwords, tokens, etc.) is automatically filtered
- Log directories should be placed outside the web root
- Proper permissions should be set on log directories
- Consider implementing log rotation for long-term storage

## Requirements

- PHP 7.4 or higher
- Write permissions on the log directory
- Sufficient disk space for logs

## License

MIT License - See LICENSE file for details

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Support

For bugs, questions, and discussions, please use the [GitHub Issues](https://github.com/yourusername/http-request-logger/issues)
