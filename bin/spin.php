<?php

use IsaEken\Spinner\Frames\DefaultFrames;
use IsaEken\Spinner\LockFile;
use IsaEken\Spinner\Spinner;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Create the lock file instance.
 */
$lockFile = LockFile::getInstance();

/**
 * Create the frames array.
 */
$frames = DefaultFrames::getFrames();

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
        DefaultFrames::getFrame($frame),
        $lockFile->read()
    );

    /**
     * Print the created frame line.
     */
    print $line;

    /**
     * Cleanup printed line.
     */
    for ($i = Spinner::getTerminalWidth() - mb_strlen($line); $i > 0; $i--) {
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
    usleep(250000);
}
