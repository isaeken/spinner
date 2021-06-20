<?php

use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Spinner;

require_once __DIR__ . '/../vendor/autoload.php';

$started_at = microtime(true);
$spinner = Spinner::getInstance();

$executed_in = Spinner::run(function () use ($started_at, $spinner) {

    Spinner::getInstance()::setTitle('Starting...');
    sleep(1);

    Spinner::setTitle('Running too long process...');
    sleep(4);

    $status = rand(0, 2);
    if ($status === 2) {
        $spinner->setStatus(Status::Failed);
    }
    else if ($status === 1) {
        $spinner->setStatus(Status::Warning);
    }

    $spinner->setTitle('Stopping...');
    sleep(1);

    return intval(microtime(true) - $started_at);
});

echo 'Process executed in ' . $executed_in . ' seconds.';
