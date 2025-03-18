<?php

namespace AnhNQ\CustomLogger\Logging;

use Illuminate\Http\Request;
use Monolog\Logger;

class LoggerCustom
{
    protected $prefix;

    public function __construct()
    {
        $this->prefix = config('custom-logger.prefix', '');
    }

    public function __invoke($logger)
    {
        $request = app(Request::class);
        
        // Create handler
        $level = config('custom-logger.level', Logger::DEBUG);
        $handler = new CustomLogHandler($request, $level);
        
        // Add handler to logger
        $logger->pushHandler($handler);
        
        return $logger;
    }
}