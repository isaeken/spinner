<?php


namespace IsaEken\Spinner;


use Exception;
use IsaEken\Spinner\Themes\DefaultTheme;

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
            static::$instance = (new LockFile())->serialize();
        }

        return static::$instance;
    }

    /**
     * @return static
     */
    public static function flush(): static
    {
        return static::$instance = (new static())->serialize();
    }

    /**
     * @var array $variables
     */
    private array $variables = [
        'theme_class' => DefaultTheme::class,
        'frame_speed' => 0.025, // seconds
        'title' => '',
    ];

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return sys_get_temp_dir() . '/spin.lock';
    }

    /**
     * @return $this
     */
    public function serialize(): static
    {
        file_put_contents($this->getFilePath(), serialize(json_encode($this->variables)));
        return $this;
    }

    /**
     * @return $this
     */
    public function unserialize(): static
    {
        try {
            $unserialize = @json_decode(unserialize(file_get_contents($this->getFilePath())), true);
        }
        catch (Exception $exception) {
            $unserialize = [];
        }

        foreach ($unserialize as $key => $value) {
            $this->variables[$key] = $value;
        }

        return $this;
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
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->variables[$name];
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $this->variables[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed
    {
        return $this->__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set(string $name, mixed $value): static
    {
        $this->__set($name, $value);
        return $this;
    }
}
