<?php


namespace IsaEken\Spinner;


class LockFile
{
    /**
     * @var LockFile $instance
     */
    private static LockFile $instance;

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (! isset(static::$instance)) {
            static::$instance = new LockFile;
        }

        return static::$instance;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return sys_get_temp_dir() . '/spin.lock';
    }

    /**
     * LockFile constructor.
     */
    public function __construct()
    {
        if (! file_exists(static::getFilePath())) {
            touch(static::getFilePath());
        }
    }

    /**
     * @return string
     */
    public function read(): string
    {
        return file_get_contents(static::getFilePath());
    }

    /**
     * @param string $contents
     * @return static
     */
    public function update(string $contents): static
    {
        file_put_contents(static::getFilePath(), $contents);
        return static::getInstance();
    }

    /**
     * @param string $contents
     * @return static
     */
    public function write(string $contents): static
    {
        file_put_contents(static::getFilePath(), static::read() . $contents);
        return static::getInstance();
    }

    /**
     * @param string $line
     * @return static
     */
    public function writeln(string $line): static
    {
        return $this->write($line . PHP_EOL);
    }
}
