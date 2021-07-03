<?php


namespace IsaEken\Spinner;


use Closure;
use Exception;
use IsaEken\Spinner\Enums\OperatingSystem;
use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Interfaces\ThemeInterface;

class Spinner
{
    /**
     * @var Spinner $spinner
     */
    private static Spinner $spinner;

    /**
     * @var PhpProcess $process
     */
    private PhpProcess $process;

    /**
     * @var mixed $output
     */
    private mixed $output = null;

    /**
     * @var string $status
     */
    private string $status = Status::Success;

    /**
     * @return static
     * @throws Exception
     */
    public static function getInstance(): static
    {
        if (! isset(static::$spinner)) {
            if (! Helpers::isCli()) {
                throw new Exception('Spinner is only can run in cli mode.');
            }

            static::$spinner = new static;
        }

        return static::$spinner;
    }

    /**
     * @return static
     */
    public static function flush(): static
    {
        LockFile::flush();
        return static::$spinner = new static;
    }

    /**
     * @return PhpProcess
     */
    public function getProcess(): PhpProcess
    {
        return $this->process;
    }

    /**
     * @return mixed
     */
    public function getOutput(): mixed
    {
        return $this->output;
    }

    /**
     * @return static
     * @throws Exception
     */
    public function start(): static
    {
        $this->process = (new PhpProcess(
            realpath(__DIR__  . '/../bin/spin.php'),
            realpath(__DIR__ . '/../'),
        ))->run();
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function stop(): static
    {
        usleep(100000);
        $this->getProcess()->kill();

        /** @var ThemeInterface $theme */
        $theme = LockFile::getInstance()->get('theme_class');

        $line = sprintf(
            '%s%s%s%s %s%s',
            chr(27),
            Helpers::getOperatingSystem() === OperatingSystem::Windows ? '[0G' : "\033[1A\033[1A",
            $theme::colors()[$this->status],
            $theme::icons()[$this->status],
            $theme::messages()[$this->status],
            "\e[39m",
        );

        print $line;
        for ($i = Helpers::getTerminalWidth() - mb_strlen($line); $i > 0; $i--) {
            print ' ';
        }
        print PHP_EOL;

        return $this;
    }

    /**
     * @param Closure|callable $closure
     * @return static
     */
    public function call(Closure|callable $closure): static
    {
        $this->output = $closure();
        return $this;
    }

    /**
     * @param string|null $title
     * @return static
     */
    public static function setTitle(string|null $title): static
    {
        if (is_string($title)) {
            LockFile::getInstance()
                ->unserialize()
                ->set('title', $title)
                ->serialize();
        }

        return static::getInstance();
    }

    /**
     * @param string $status
     * @return static
     */
    public static function setStatus(string $status = Status::Success): static
    {
        $instance = static::getInstance();
        $instance->status = $status;
        return $instance;
    }

    /**
     * @param ThemeInterface|string|null $theme
     * @return static
     */
    public static function setTheme(ThemeInterface|string|null $theme): static
    {
        if ($theme instanceof ThemeInterface) {
            LockFile::getInstance()->set('theme_class', $theme::class)->serialize();
        }
        else if (is_string($theme)) {
            LockFile::getInstance()->set('theme_class', $theme)->serialize();
        }

        return static::getInstance();
    }

    /**
     * @param Closure|callable $closure
     * @param ThemeInterface|string|null $theme
     * @param string|null $title
     * @return mixed
     * @throws Exception
     */
    public static function run(
        Closure|Callable $closure,
        ThemeInterface|string|null $theme = null,
        string|null $title = 'Please wait...'
    ): mixed
    {
        $instance = static::getInstance();
        $terminate = function () use ($instance) {
            $instance->getProcess()->kill();
            print PHP_EOL . "\e[39m" . 'Process Terminated.' . PHP_EOL;
            exit;
        };

        if (Helpers::getOperatingSystem() === OperatingSystem::Windows) {
            sapi_windows_set_ctrl_handler($terminate);
        }
        else if (Helpers::getOperatingSystem() === OperatingSystem::Linux) {
            if (extension_loaded('pcntl')) {
                declare(ticks = 1);
                pcntl_signal(SIGINT, $terminate);
            }
        }

        $instance
            ->setTheme($theme)
            ->setTitle($title)
            ->start()
            ->call($closure)
            ->stop();

        $output = $instance->getOutput();
        $instance->flush();

        return $output;
    }
}
