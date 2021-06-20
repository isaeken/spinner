<?php


namespace IsaEken\Spinner\Interfaces;


use Illuminate\Support\Collection;

interface ThemeInterface
{
    /**
     * Get the frame collection.
     *
     * @return Collection
     */
    public static function frames(): Collection;

    /**
     * Get the status icon collection.
     *
     * @return Collection
     */
    public static function icons(): Collection;

    /**
     * Get the status message collection.
     *
     * @return Collection
     */
    public static function messages(): Collection;

    /**
     * Get the color collection.
     *
     * @return Collection
     */
    public static function colors(): Collection;
}
