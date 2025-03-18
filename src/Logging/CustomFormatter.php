<?php

namespace AnhNQ\CustomLogger\Logging;

use Monolog\Formatter\LineFormatter;
use Illuminate\Http\Request;

class CustomFormatter extends LineFormatter
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    protected $request;
    protected $isConsole;

    public function __construct()
    {
        $this->isConsole = app()->runningInConsole();
        $this->request = $this->isConsole ? null : app(Request::class);
        
        $dateFormat = config('custom-logger.date_format', self::DATE_FORMAT);
        $output = config('custom-logger.format', "{'time':%datetime%, 'level':%channel%.%level_name%, 'action_type': %message%, 'method': %extra.method%, 'user': %extra.user%, 'context': %context%, 'url': %extra.url%, 'params': %extra.params%, 'ip': %extra.ip%, 'user-agent': %extra.user-agent%}");
        parent::__construct($output, $dateFormat, true, true);
    }

    public function format(array $record): string
    {
        if ($this->isConsole) {
            $this->addConsoleContext($record);
        } else {
            $this->addRequestContext($record);
        }

         // Handle exceptions
         if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $record['extra']['exception_class'] = get_class($record['context']['exception']);
            $record['extra']['file'] = $record['context']['exception']->getFile();
            $record['extra']['line'] = $record['context']['exception']->getLine();
            // $record['extra']['trace'] = explode("\n", $record['context']['exception']->getTraceAsString());
        }

        return parent::format($record);
    }

    protected function addRequestContext(array &$record)
    {
        if (!$this->request) {
            return;
        }
        
        // Request info
        $record['extra']['url'] = $this->request->fullUrl();
        $record['extra']['ip'] = $this->request->ip();
        $record['extra']['method'] = $this->request->getMethod();
        $record['extra']['user_agent'] = $this->request->userAgent();
        
        // User info
        if (auth()->check()) {
            $record['extra']['user_id'] = auth()->id();
        }
        
        // Request params
        if (config('custom-logger.context_data.params', true)) {
            $record['extra']['params'] = $this->maskSensitiveData($this->request->all());
        }
    }

    protected function addConsoleContext(array &$record)
    {
        $command = $_SERVER['argv'] ?? [];
        
        $record['extra']['environment'] = 'console';
        $record['extra']['command'] = !empty($command) ? implode(' ', $command) : null;
        
        // Add current user if running as specific user
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $user = posix_getpwuid(posix_geteuid());
            $record['extra']['system_user'] = $user['name'] ?? null;
        }
    }

    protected function maskSensitiveData(array $data)
    {
        $sensitiveKeys = config('custom-logger.mask_fields', ['password', 'token', 'secret', 'key']);
        
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '******';
            } else if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            }
        }
        
        return $data;
    }
}