<?php

use IsaEken\Spinner\LockFile;
use IsaEken\Spinner\Spinner;

require_once __DIR__ . '/../vendor/autoload.php';

$lockFile = LockFile::getInstance();

$frames = [
    '⠋',
    '⠙',
    '⠹',
    '⠸',
    '⠼',
    '⠴',
    '⠦',
    '⠧',
    '⠇',
    '⠏',
];
$frame = 0;

while (true) {
    $line = sprintf(
        '%s%s%s %s',
        chr(27),
        '[0G',
        $frames[$frame],
        $lockFile->read()
    );

    print $line;
    for ($i = Spinner::getTerminalWidth() - mb_strlen($line); $i > 0; $i--) {
        print ' ';
    }

//    print chr(27) . "[0G" . $frames[$frame] . " " . $lockFile->read();
//    print getenv('COLUMNS');

    $frame++;
    if ($frame >= count($frames)) {
        $frame = 0;
    }

    usleep(250000);
}
