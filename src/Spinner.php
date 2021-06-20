<?php


namespace IsaEken\Spinner;


use Closure;
use Exception;
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
     */
    public static function getInstance(): static
    {
        if (! isset(static::$spinner)) {
            static::$spinner = new static;
        }

        return static::$spinner;
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
        $this->getProcess()->kill();
        usleep(10000);

        /** @var ThemeInterface $theme */
        $theme = LockFile::getInstance()->get('theme_class');

        $line = sprintf(
            '%s%s%s%s %s%s',
            chr(27),
            '[0G',
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
     * @param string $title
     * @return static
     */
    public static function setTitle(string $title): static
    {
        $instance = static::getInstance();
        LockFile::getInstance()
            ->unserialize()
            ->set('title', $title)
            ->serialize();
        return $instance;
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
     * @param Closure|callable $closure
     * @return mixed
     * @throws Exception
     */
    public static function run(Closure|Callable $closure): mixed
    {
        return static::getInstance()
            ->start()
            ->call($closure)
            ->stop()
            ->getOutput();
    }
}
