<?php


namespace IsaEken\Spinner;


use Exception;
use IsaEken\Spinner\Enums\OperatingSystem;

class PhpProcess
{
    /**
     * @var array $variables
     */
    private array $variables = [
        'script' => null,
        'cwd' => __DIR__,
        'binary' => PHP_BINARY,
        'stdin' => STDIN,
        'stdout' => STDOUT,
        'pid' => null,
    ];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        return $this->$name;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        if (array_key_exists($name, $this->variables)) {
            $this->variables[$name] = $value;
        }

        $this->$name = $value;
    }

    /**
     * PhpProcess constructor.
     *
     * @param string $script
     * @param string $cwd
     */
    public function __construct(string $script, string $cwd)
    {
        $this->__set('script', $script);
        $this->__set('cwd', $cwd);
    }

    /**
     * @return string
     */
    public function generateCommand(): string
    {
        return sprintf(
            '%s %s',
            $this->__get('binary'),
            $this->__get('script'),
        );
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->__get('pid') != null;
    }

    /**
     * @return static
     * @throws Exception
     */
    public function run(): static
    {
        if ($this->isRunning()) {
            throw new Exception('Process is already started.');
        }

        $os = Helpers::getOperatingSystem();
        $descriptor_spec = [$this->__get('stdin'), $this->__get('stdout')];
        $cwd = $this->__get('cwd');

        if ($os === OperatingSystem::Windows) {
            $process = proc_open(
                'start /b ' . $this->generateCommand(),
                $descriptor_spec,
                $pipes,
                $cwd,
                null,
            );

            if (is_resource($process)) {
                $pid = proc_get_status($process)['pid'];
            }
            else {
                throw new Exception('Failed to execute child process.');
            }

            $output = array_filter(explode(' ', shell_exec(sprintf(
                'wmic process get parentprocessid,processid | find "%s"',
                $pid
            ))));
            array_pop($output);
            $this->__set('pid', end($output));
        }
        else if ($os === OperatingSystem::Linux) {
            $process = proc_open(
                $this->generateCommand(),
                $descriptor_spec,
                $pipes,
                $cwd,
                null,
            );

            if (is_resource($process)) {
                $this->__set('pid', proc_get_status($process)['pid'] + 1);
            }
            else {
                throw new Exception('Failed to execute child process.');
            }
        }
        else {
            throw new Exception('Unsupported platform!');
        }

        return $this;
    }

    /**
     * @return static
     * @throws Exception
     */
    public function kill(): static
    {
        if (! $this->isRunning()) {
            throw new Exception('Process is not started.');
        }

        $os = Helpers::getOperatingSystem();

        if ($os === OperatingSystem::Windows) {
            exec(sprintf('taskkill /pid %s /F', $this->__get('pid')));
            $this->__set('pid', null);
        }
        else if ($os === OperatingSystem::Linux) {
            exec(sprintf('kill -9 %s', $this->__get('pid')));
            $this->__set('pid', null);
        }
        else {
            throw new Exception('Unsupported platform!');
        }

        return $this;
    }
}
