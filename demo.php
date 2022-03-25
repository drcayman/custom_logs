<?php
require 'vendor/autoload.php';
use drcayman\custom_logs\Log;
Log::initialize();
$data['message'] = "111";

Log::record($data);
Log::FlushLogs();

