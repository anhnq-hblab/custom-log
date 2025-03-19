# CustomLogger for Laravel

A flexible, feature-rich logging package for Laravel applications that enhances the default logging capabilities with customizable formatting, sensitive data masking, and comprehensive context information.

## Features

- Customizable log format with JSON structure
- Automatic context collection from web requests and console commands
- Sensitive data masking (passwords, tokens, credit cards, etc.)
- Exception details capturing (class, file, line)
- Log level filtering
- Daily log file rotation support
- Works in both web and console environments

## Requirements

- PHP ^7.4 or ^8.0
- Laravel ^8.0, ^9.0, or ^10.0

## Installation


## Installation

### Via Git URL

Add the repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/anhnq-hblab/custom-log"
    }
]
```

```bash
composer require anhnq/custom-log:@dev
```


### Local Development

1. Clone the repository:


```bash
mkdir -p packages/AnhNQ
cd packages/AnhNQ
git clone https://github.com/anhnq-hblab/custom-log.git CustomLogger
```

2. Add the repository to your Laravel project's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/AnhNQ/CustomLogger"
    }
]
```

3. Require the package:

```bash
composer require anhnq/custom-log:@dev
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=logger-config
```

This will create `config/custom-logger.php` with the following options:

```php
return [
    'path' => storage_path('logs/custom.log'),
    'enabled' => env('CUSTOM_LOGGER_ENABLED', true),
    'level' => env('CUSTOM_LOGGER_LEVEL', Logger::ERROR),
    'date_format' => 'Y-m-d H:i:s',
    'format' => "{'time': '%datetime%', 'channel': '%channel%', 'level': '%level_name%' , 'message': '%message%', 'extra': %extra%}",
    'mask_fields' => [
        'password',
        'token',
        'secret',
        'key',
        'api_key',
        'csrf',
        'credit_card',
        'email'
    ],
];
```

## Usage

The package integrates automatically with Laravel's logging system:

```php
// Basic logging
Log::info('User login successful');

// Logging with context
Log::error('Payment failed', ['order_id' => 123, 'amount' => 99.99]);

// Logging exceptions
try {
    // Code that might throw an exception
} catch (\Exception $e) {
    Log::error('An error occurred', ['exception' => $e]);
}
```

## Log Structure

Logs are formatted as JSON with the following structure:

```json
{
  "time": "2023-04-15 14:32:45",
  "channel": "custom",
  "level": "ERROR",
  "message": "Payment failed",
  "extra": {
    "url": "https://example.com/checkout",
    "ip": "192.168.1.1",
    "method": "POST",
    "user_agent": "Mozilla/5.0...",
    "user_id": 42,
    "params": {
      "order_id": 123,
      "amount": 99.99,
      "credit_card": "******"
    },
    "exception_class": "PaymentException",
    "file": "/app/Services/PaymentService.php",
    "line": 45
  }
}
```

## Advanced Configuration

### Custom Formatter

You can customize the formatter by publishing it:

```bash
php artisan vendor:publish --tag=logger-format
```

This will create `app/Logging/LoggerCustom.php` which you can modify to fit your needs.

### Environment Variables

```
CUSTOM_LOGGER_ENABLED=true
CUSTOM_LOGGER_LEVEL=200  # ERROR level
```

## License

This package is open-sourced software licensed under the MIT license.