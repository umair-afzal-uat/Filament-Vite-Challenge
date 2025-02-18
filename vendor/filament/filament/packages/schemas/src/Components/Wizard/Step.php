<?php

namespace Filament\Schemas\Components\Wizard;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Illuminate\Support\Str;

class Step extends Component implements CanConcealComponents
{
    protected ?Closure $afterValidation = null;

    protected ?Closure $beforeValidation = null;

    protected string | Closure | null $description = null;

    protected string | BackedEnum | Closure | null $icon = null;

    protected string | BackedEnum | Closure | null $completedIcon = null;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.wizard.step';

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(fn (Step $component): string => Str::slug(Str::transliterate($component->getLabel(), strict: true)));
    }

    public function afterValidation(?Closure $callback): static
    {
        $this->afterValidation = $callback;

        return $this;
    }

    /**
     * @deprecated Use `afterValidation()` instead.
     */
    public function afterValidated(?Closure $callback): static
    {
        $this->afterValidation($callback);

        return $this;
    }

    public function beforeValidation(?Closure $callback): static
    {
        $this->beforeValidation = $callback;

        return $this;
    }

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string | BackedEnum | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function completedIcon(string | BackedEnum | Closure | null $icon): static
    {
        $this->completedIcon = $icon;

        return $this;
    }

    public function callAfterValidation(): void
    {
        $this->evaluate($this->afterValidation);
    }

    public function callBeforeValidation(): void
    {
        $this->evaluate($this->beforeValidation);
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function getIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->icon);
    }

    public function getCompletedIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->completedIcon);
    }

    /**
     * @return array<string, int | null>
     */
    public function getColumnsConfig(): array
    {
        return $this->columns ?? $this->getContainer()->getColumnsConfig();
    }

    public function canConcealComponents(): bool
    {
        return true;
    }
}
