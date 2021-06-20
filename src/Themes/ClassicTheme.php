<?php


namespace IsaEken\Spinner\Themes;


use Illuminate\Support\Collection;
use IsaEken\Spinner\Enums\Status;
use IsaEken\Spinner\Interfaces\ThemeInterface;

class ClassicTheme extends DefaultTheme implements ThemeInterface
{
    /**
     * @inheritDoc
     */
    public static function frames(): Collection
    {
        return collect([
            '-',
            '\\',
            '|',
            '/',
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function icons(): Collection
    {
        return collect([
            Status::Success => ' - SUCCESS - ',
            Status::Warning => ' - WARNING - ',
            Status::Failed  => ' -  FAIL  - ',
        ]);
    }
}
