<?php

use IsaEken\Spinner\Spinner;

require_once __DIR__ . '/vendor/autoload.php';

$started_at = microtime(true);
$executed_in = Spinner::run(function () use ($started_at) {
    Spinner::setTitle('Starting...');
    sleep(1);
    Spinner::setTitle('Running too long process...');
    sleep(4);
    Spinner::setTitle('Stopping...');
    sleep(1);
    return intval(microtime(true) - $started_at);
});

echo 'Process executed in ' . $executed_in . ' seconds.';
