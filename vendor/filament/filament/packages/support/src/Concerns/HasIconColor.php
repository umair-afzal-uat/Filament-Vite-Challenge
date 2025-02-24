<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasIconColor
{
    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string | array | Closure | null $iconColor = null;

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function iconColor(string | array | Closure | null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getIconColor(): string | array | null
    {
        return $this->evaluate($this->iconColor);
    }
}
