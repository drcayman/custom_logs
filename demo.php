<?php
require 'src/Log.php';
use drcayman\custom_logs\Log;
echo "111";

$data['message'] = "111";

Log::record($data);
Log::FlushLogs();

