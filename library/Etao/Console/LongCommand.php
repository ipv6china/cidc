#!/usr/local/bin/php -q
<?php
include 'Exec.php';
$cmd = urldecode($argv[1]);
if(strlen($cmd) > 0) {
    @$success_url = urldecode($argv[2]);
    @$fail_url = urldecode($argv[3]);
    $start_time = date('Y-m-d H:i:s', time());
    $start_time_m = microtime(TRUE);
//exec($cmd,$output,$status);
    Etao_Console_Exec::exec($cmd, $output, $status);
    $finish_time = date('Y-m-d H:i:s', time());
    echo 'CRS CMD log' . "\n";
    echo '执行命令: ' . $cmd . "\n";
    echo '执行耗时: ' . (microtime(TRUE) - $start_time_m) . " s \n";
    echo '开始时间: ' . $start_time . "\n";
    echo '结束时间: ' . $finish_time . "\n";
    echo '执行结果:' . "\n";
    print_r($output);
    if($status) {
        if(strlen($fail_url) > 0) {
            file_get_contents($fail_url);
        }
    } else {
        if(strlen($success_url) > 0) {
            file_get_contents($success_url);
        }
    }
}
