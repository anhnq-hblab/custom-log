<?php

namespace AnhNQ\CustomLogger\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Http\Request;
use Monolog\Logger;

class CustomLogHandler extends AbstractProcessingHandler
{
    protected $request;
    protected $logPath;

    public function __construct(Request $request = null, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->request = $request ?? app(Request::class);
        $this->logPath = config('custom-logger.log_path', storage_path('logs/custom.log'));
        $this->setFormatter(new CustomFormatter($this->request));
    }

    protected function write(array $record): void
    {
        if (!config('custom-logger.enabled', true) || $record['level'] < config('custom-logger.level', Logger::DEBUG)) {
            return;
        }
        $logFile = $this->getLogFilePath();
        file_put_contents($logFile, $record['formatted'] . PHP_EOL, FILE_APPEND);
    }

    protected function getLogFilePath()
    {
        $dateSuffix = '';
        
        if (config('custom-logger.daily_files', true)) {
            $dateSuffix = '-' . date('Y-m-d');
        }
        
        $pathInfo = pathinfo($this->logPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $dateSuffix . '.' . ($pathInfo['extension'] ?? 'log');
    }
}