<?php


namespace IsaEken\Spinner\Interfaces;


interface FrameInterface
{
    /**
     * Get the frame count.
     *
     * @return int
     */
    public static function getFrameCount(): int;

    /**
     * Get frames as array.
     *
     * @return string[]
     */
    public static function getFrames(): array;

    /**
     * Get a specific frame.
     *
     * @param int $index
     * @return string
     */
    public static function getFrame(int $index): string;
}
