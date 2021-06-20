<?php


namespace IsaEken\Spinner\Themes;


use Illuminate\Support\Collection;
use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Interfaces\ThemeInterface;

class DefaultTheme implements ThemeInterface
{
    /**
     * @inheritDoc
     */
    public static function frames(): Collection
    {
        return collect([
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
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function icons(): Collection
    {
        return collect([
            Status::Success => '✔️',
            Status::Warning => '⚠️',
            Status::Failed  => '❌',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function messages(): Collection
    {
        return collect([
            Status::Success => 'Process successfully completed.',
            Status::Warning => 'Process completed but the warnings alerted.',
            Status::Failed  => 'Process cannot be completed successfully.',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function colors(): Collection
    {
        return collect([
            Status::Success => "\e[32m",
            Status::Warning => "\e[33m",
            Status::Failed  => "\e[31m",
        ]);
    }
}
