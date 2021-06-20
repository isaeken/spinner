<?php


namespace IsaEken\Spinner;


use Closure;

class Spinner
{
    /**
     * @var Spinner $spinner
     */
    private static Spinner $spinner;

    /**
     * @var LockFile $lockFile
     */
    private static LockFile $lockFile;

    /**
     * @var $pid
     */
    private static $pid;

    /**
     * @var mixed $output
     */
    public static mixed $output = null;

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (! isset(static::$spinner)) {
            static::$spinner = new static;
        }

        return static::$spinner;
    }

    /**
     * @return LockFile
     */
    public static function getLockFile(): LockFile
    {
        if (! isset(static::$lockFile)) {
            static::$lockFile = new LockFile;
        }

        return static::$lockFile;
    }

    /**
     * @return mixed
     */
    public static function getOutput(): mixed
    {
        return static::$output;
    }

    /**
     * @return int
     */
    public static function getTerminalWidth(): int
    {
        $os = php_uname('s');
        if ($os === 'Windows NT') {
            return intval(exec(__DIR__ . '/../bin/get_width.bat'));
        }
        else if ($os === 'Linux') {
            list($rows, $cols) = explode(' ', @exec('stty size 2>/dev/null') ?: '0 0');
            return intval($cols);
        }

        return 0;
    }

    /**
     * @param string $title
     * @return static
     */
    public static function setTitle(string $title): static
    {
        static::getLockFile()->update($title);
        return static::getInstance();
    }

    /**
     * @param string|null $title
     * @return static
     */
    public static function start(string|null $title = null): static
    {
        $instance = static::getInstance();

        $os = php_uname('s');
        $cwd = realpath(__DIR__ . '/../');
        $bin = PHP_BINARY;
        $script = __DIR__ . '/../bin/spin.php';
        if ($title !== null && mb_strlen($title) > 0) {
            static::getLockFile()->write($title);
        }

        $cmd = sprintf('%s %s', $bin, $script);

        if ($os === 'Windows NT') {
            if (is_resource($process = proc_open('start /b ' . $cmd, [STDIN, STDOUT], $pipes, $cwd, NULL))) {
                $ppid = proc_get_status($process);
                $pid = $ppid['pid'];
            }
            else {
                echo 'Failed to execute child process.';
            }

            $output = array_filter(explode(" ", shell_exec("wmic process get parentprocessid,processid | find \"$pid\"")));
            array_pop($output);
            static::$pid = end($output);
        }
        else if ($os === 'Linux') {
            if (is_resource($process = proc_open('nohup ' . $cmd, [[STDIN, STDOUT]], $pipes, $cwd, NULL))) {
                $ppid = proc_get_status($process);
                $pid = $ppid['pid'];
                static::$pid = $pid + 1;
            }
            else {
                echo 'Failed to execute child process.';
            }
        }

        return $instance;
    }

    /**
     * @return $this
     */
    public static function stop(): static
    {
        $os = php_uname('s');
        if ($os == 'Windows NT') {
            exec(sprintf('taskkill /pid %s /F', static::$pid));
        }
        else if ($os == 'Linux') {
            exec(sprintf('kill -9 %s', static::$pid));
        }

        usleep(10000);

        $line = sprintf(
            '%s%s%s %s',
            chr(27),
            '[0G',
            '✔️',
            'Completed.'
        );
        print $line;
        for ($i = Spinner::getTerminalWidth() - mb_strlen($line); $i > 0; $i--) {
            print ' ';
        }
        print PHP_EOL;

        return static::getInstance();
    }

    /**
     * @param Closure|callable $closure
     * @return static
     */
    public static function call(Closure|callable $closure): static
    {
        $instance = static::getInstance();
        $instance::$output = $closure();
        return $instance;
    }

    /**
     * @param Closure|callable $closure
     * @param string|null $title
     * @return mixed
     */
    public static function run(Closure|Callable $closure, string|null $title = null): mixed
    {
        return static::start($title)::call($closure)::stop()::getOutput();
    }
}
