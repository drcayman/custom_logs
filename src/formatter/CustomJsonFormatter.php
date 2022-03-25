<?php
namespace drcayman\custom_logs\formatter;


use Monolog\Formatter\FormatterInterface;


class CustomJsonFormatter implements FormatterInterface
{

    public function format(array $record)
    {
        return json_encode($record).PHP_EOL;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }
        return $message;
    }
}