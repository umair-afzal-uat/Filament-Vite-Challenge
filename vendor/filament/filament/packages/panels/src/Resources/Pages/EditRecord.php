<?php

namespace Filament\Resources\Pages;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\NestedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Throwable;

use function Filament\Support\is_app_url;

/**
 * @property-read Schema $form
 */
class EditRecord extends Page
{
    use CanUseDatabaseTransactions;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use HasUnsavedDataChangesAlert;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?string $previousUrl = null;

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::resources.pages.edit-record.navigation-item')
            ?? Heroicon::OutlinedPencilSquare;
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/edit-record.breadcrumb');
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament-panels::resources/pages/edit-record.content.tab.label');
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected function fillForm(): void
    {
        /** @internal Read the DocBlock above the following method. */
        $this->fillFormWithDataAndCallHooks($this->getRecord());
    }

    /**
     * @internal Never override or call this method. If you completely override `fillForm()`, copy the contents of this method into your override.
     *
     * @param  array<string, mixed>  $extraData
     */
    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->attributesToArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string>  $statePaths
     */
    public function refreshFormData(array $statePaths): void
    {
        $this->form->fillPartially(
            $this->mutateFormDataBeforeFill($this->getRecord()->attributesToArray()),
            $statePaths,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState(afterValidate: function (): void {
                $this->callHook('afterValidate');

                $this->callHook('beforeSave');
            });

            $data = $this->mutateFormDataBeforeSave($data);

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    public function saveFormComponentOnly(Component $component): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = Schema::make($component->getLivewire())
                ->schema([$component])
                ->model($component->getRecord())
                ->statePath($this->getFormStatePath())
                ->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();
    }

    protected function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? __('filament-panels::resources/pages/edit-record.notifications.saved.title');
    }

    /**
     * @deprecated Use `getSavedNotificationTitle()` instead.
     */
    protected function getSavedNotificationMessage(): ?string
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public function configureForm(Schema $schema): Schema
    {
        $schema->columns($this->hasInlineLabels() ? 1 : 2);
        $schema->inlineLabel($this->hasInlineLabels());

        static::getResource()::form($schema);

        $this->form($schema);

        return $schema;
    }

    public function getDefaultActionSchemaResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof CreateAction => fn (Schema $schema): Schema => static::getResource()::form($schema->columns(2)),
            $action instanceof EditAction => fn (Schema $schema): Schema => $this->configureForm($schema),
            $action instanceof ViewAction => fn (Schema $schema): Schema => static::getResource()::infolist(static::getResource()::form($schema->columns(2))),
            default => null,
        };
    }

    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/edit-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        $hasFormWrapper = $this->hasFormWrapper();

        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->submit($hasFormWrapper ? $this->getSubmitFormLivewireMethodName() : null)
            ->action($hasFormWrapper ? null : $this->getSubmitFormLivewireMethodName())
            ->keyBindings(['mod+s']);
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getSaveFormAction();
    }

    protected function getSubmitFormLivewireMethodName(): string
    {
        return 'save';
    }

    protected function getCancelFormAction(): Action
    {
        $url = $this->previousUrl ?? $this->getResourceUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler(
                FilamentView::hasSpaMode($url)
                    ? 'document.referrer ? window.history.back() : Livewire.navigate(' . Js::from($url) . ')'
                    : 'document.referrer ? window.history.back() : (window.location.href = ' . Js::from($url) . ')',
            )
            ->color('gray');
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    /**
     * @return array<int | string, string | Schema>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->configureForm(
                $this->makeSchema()
                    ->schema($this->getFormSchema())
                    ->operation('edit')
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath()),
            ),
        ];
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation($parameters) && static::getResource()::canEdit($parameters['record']);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...($this->hasCombinedRelationManagerTabsWithContent() ? [] : $this->getContentComponents()),
                ...$this->getRelationManagersContentComponents(),
            ]);
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getContentComponents(): array
    {
        return [
            ...$this->getFormContentComponents(),
        ];
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormContentComponents(): array
    {
        $formSchema = NestedSchema::make('form');
        $actions = Actions::make($this->getFormActions())
            ->alignment($this->getFormActionsAlignment())
            ->fullWidth($this->hasFullWidthFormActions())
            ->sticky($this->areFormActionsSticky());

        if ($this->hasFormWrapper()) {
            return [
                $formSchema,
                $actions,
            ];
        }

        return [
            Form::make([$formSchema])
                ->id('form')
                ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName())
                ->footer([
                    $actions,
                ]),
        ];
    }

    public function hasFormWrapper(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-edit-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
