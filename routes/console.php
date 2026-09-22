<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('db:binlog-test', function () {
    $idx = '/var/lib/mysql/binlog.index';
    if (file_exists($idx) && is_readable($idx)) {
        $this->info("Index readable: " . file_get_contents($idx));
    } else {
        $this->error("Index not readable or not found");
    }
});
