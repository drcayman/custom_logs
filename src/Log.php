<?php
namespace drcayman\custom_logs;
use drcayman\custom_logs\formatter\CustomJsonFormatter;
use Exception;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{

    private static $_file_directory;
    private static $_file_size;
    private static $_rotate_hourly;
    private static $_file_prefix;
    private static $_file_name;
    private static $_count = 0;
    private static $_logger;
    private static $_formatter;
    private static $_level = Logger::INFO;
    private static $_log_queue;

    private function __construct(){

    }

    /**
     * @param string $file_directory 日志文件目录
     * @param int $file_size  文件切割大小
     * @param bool $rotate_hourly 是否按小时切分文件
     * @param string $file_prefix 生成的日志文件前缀
     * @param FormatterInterface $formatter 格式化组件
     * @return void
     */
    public static function initialize($file_directory = '.', $file_size = 0, $rotate_hourly = false, $file_prefix = '',FormatterInterface $formatter = null){

        self::$_file_directory = $file_directory;
        if (!is_dir($file_directory)) {
            mkdir($file_directory, 0777, true);
        }
        self::$_file_size = $file_size;
        self::$_rotate_hourly = $rotate_hourly;
        self::$_file_prefix = $file_prefix;
        if(empty($formatter)){
            self::$_formatter = new CustomJsonFormatter();
        }

    }


    /**
     * 记录日志
     * @param array $log
     * @return void
     */
    public static function record(array $log)
    {
        self::$_log_queue[] = $log;

    }


    /**
     * 记录日志
     * @return void
     */
    public static function FlushLogs(){

        if(count(self::$_log_queue) <= 0)
            return;
        if(self::$_file_name != self::getFileName()){
            self::LogInit();
        }
        foreach (self::$_log_queue as $log_array)
        {
            $encoded = json_encode($log_array);
            self::$_logger->info($encoded);

        }
        self::$_log_queue = [];
    }



    /**
     * monolog 初始化
     * @return void
     */
    public static function LogInit(){

        self::$_logger = new Logger(self::$_file_prefix);
        try
        {
            $handler = new StreamHandler
            (
                self::$_file_name,
                self::$_level,
                true,
                0777
            );

            $handler->setFormatter(self::$_formatter);
        }
        catch(Exception $e)
        {
            return;
        }
        self::$_logger->pushHandler($handler);

    }




    /**
     * 获取文件名
     * @return string
     */
    public static function getFileName(){
        $date_format = self::$_rotate_hourly ? 'Y-m-d-H' : 'Y-m-d';
        $file_prefix = self::$_file_prefix == '' ? '' : self::$_file_prefix . '.';
        $file_base = self::$_file_directory . '/' . $file_prefix . 'log.' . date($date_format, time()) . "_";
        $count = self::$_count;
        $file_complete = $file_base . $count;
        if (self::$_file_size > 0) {
            while (file_exists($file_complete) && self::fileSizeOut($file_complete)) {
                $count += 1;
                $file_complete = $file_base . $count;
            }
            self::$_count = $count; // cli 模式下记录到第几个文件 减少文件查找
        }
        self::$_file_name = $file_complete;
        return $file_complete;
    }

    /**
     * 获取当前文件大小
     * @param $fp
     * @return bool
     */
    public static function fileSizeOut($fp)
    {
        clearstatcache();
        $fpSize = filesize($fp) / (1024 * 1024);
        if ($fpSize >= self::$_file_size) {
            return true;
        } else {
            return false;
        }
    }


}