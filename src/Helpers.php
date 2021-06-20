<?php


namespace IsaEken\Spinner;


use Illuminate\Support\Str;
use IsaEken\Spinner\Enums\OperatingSystem;

class Helpers
{
    /**
     * @return string
     */
    public static function getOperatingSystem(): string
    {
        $os = Str::of(php_uname('s'))->trim()->lower();

        if ($os->contains(OperatingSystem::Windows)) {
            return OperatingSystem::Windows;
        }
        else if ($os->contains(OperatingSystem::Linux)) {
            return OperatingSystem::Linux;
        }

        return OperatingSystem::Unknown;
    }

    /**
     * @return int
     */
    public static function getTerminalWidth(): int
    {
        $os = static::getOperatingSystem();

        if ($os === OperatingSystem::Linux) {
            list($rows, $columns) = explode(' ', @exec('stty size 2>/dev/null') ?: '0 0');
            return intval($columns);
        }
        else if ($os === OperatingSystem::Windows) {
            return intval(exec(__DIR__ . '/../bin/get_width.bat'));
        }

        return 0;
    }
}
