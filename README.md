# custom_logs
支持按照文件大小 时间切割日志

# 示例

```
require 'vendor/autoload.php';
use drcayman\custom_logs\Log;
Log::initialize();
$data['message'] = "111";

Log::record($data); //连续记录多次
Log::FlushLogs(); //最后写入日志
```

```* @param string $file_directory 日志文件目录
* @param int $file_size  文件切割大小
* @param bool $rotate_hourly 是否按小时切分文件
* @param string $file_prefix 生成的日志文件前缀
* @param FormatterInterface $formatter 格式化组件
* @return void
Log::initialize($file_directory = '.', $file_size = 0, $rotate_hourly = false, $file_prefix = '',FormatterInterface $formatter = null);
```

