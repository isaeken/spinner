<?php


namespace IsaEken\Spinner\Frames;


use IsaEken\Spinner\Interfaces\FrameInterface;

class DefaultFrames implements FrameInterface
{
    /**
     * @inheritDoc
     */
    public static function getFrameCount(): int
    {
        return count(static::getFrames());
    }

    /**
     * @inheritDoc
     */
    public static function getFrames(): array
    {
        return [
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
    }

    /**
     * @inheritDoc
     */
    public static function getFrame(int $index): string
    {
        return static::getFrames()[$index];
    }
}
