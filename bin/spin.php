#!/usr/bin/env php
<?php

use IsaEken\Spinner\Helpers;
use IsaEken\Spinner\LockFile;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
else if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}
else {
    throw new Exception('Autoloader not found. Maybe you forgot to run the "composer install" command?');
}

/**
 * Create the lock file instance.
 */
$lockFile = LockFile::getInstance()->unserialize();

/**
 * Calculate the frame speed.
 */
$frame_speed = $lockFile->get('frame_speed') * 1000000;

/**
 * Get the theme class.
 */
$theme_class = $lockFile->get('theme_class');

/**
 * Create the frames array.
 */
$frames = $theme_class::frames();

/**
 * Set current frame index.
 */
$frame = 0;

/**
 * Update the frame while the script is running.
 */
while (true) {

    /**
     * Create the frame line.
     */
    $line = sprintf(
        '%s%s%s %s',
        chr(27),
        '[0G',
        $frames[$frame],
        $lockFile->unserialize()->get('title'),
    );

    /**
     * Print the created frame line.
     */
    print $line;

    /**
     * Cleanup printed line.
     */
    for ($i = Helpers::getTerminalWidth() - mb_strlen($line); $i > 0; $i--) {
        print ' ';
    }

    /**
     * Update frame index.
     */
    $frame++;
    if ($frame >= count($frames)) {
        $frame = 0;
    }

    /**
     * Wait the next frame.
     */
    usleep($frame_speed);
}
