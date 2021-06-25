<?php


namespace IsaEken\Spinner;


use Illuminate\Support\Str;
use IsaEken\Spinner\Enums\OperatingSystem;

class Helpers
{
    /**
     * @var string|null $operatingSystem
     */
    private static string|null $operatingSystem = null;

    /**
     * @var object|null $terminalSizes
     */
    private static object|null $terminalSizes = null;

    /**
     * @return string
     */
    public static function getOperatingSystem(): string
    {
        if (static::$operatingSystem === null) {
            $os = Str::of(php_uname('s'))->trim()->lower();

            if ($os->contains(OperatingSystem::Windows)) {
                return static::$operatingSystem = OperatingSystem::Windows;
            }
            else if ($os->contains(OperatingSystem::Linux)) {
                return static::$operatingSystem = OperatingSystem::Linux;
            }

            return static::$operatingSystem = OperatingSystem::Unknown;
        }

        return static::$operatingSystem;
    }

    /**
     * @return object
     */
    public static function getTerminalSizes(): object
    {
        if (static::$terminalSizes === null) {
            $os = static::getOperatingSystem();
            $rows = 0;
            $columns = 0;

            if ($os === OperatingSystem::Linux) {
                list($rows, $columns) = explode(' ', @exec('stty size 2>/dev/null') ?: '0 0');
            }
            else if ($os === OperatingSystem::Windows) {
                $json = json_decode(@exec(__DIR__ . '/../bin/get_terminal_size.bat'));
                $rows = $json->rows;
                $columns = $json->columns;
            }

            return static::$terminalSizes = (object) [
                'rows' => intval($rows),
                'columns' => intval($columns),
            ];
        }

        return static::$terminalSizes;
    }

    /**
     * @return int
     */
    public static function getTerminalWidth(): int
    {
        return static::getTerminalSizes()->columns;
    }

    /**
     * @return int
     */
    public static function getTerminalHeight(): int
    {
        return static::getTerminalSizes()->rows;
    }
}
