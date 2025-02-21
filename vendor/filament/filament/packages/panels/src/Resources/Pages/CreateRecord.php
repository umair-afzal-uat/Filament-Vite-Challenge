<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\NestedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Throwable;

use function Filament\Support\is_app_url;

/**
 * @property-read Schema $form
 */
class CreateRecord extends Page
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;

    public ?Model $record = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?string $previousUrl = null;

    protected static bool $canCreateAnother = true;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/create-record.breadcrumb');
    }

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canCreate(), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        if ($another) {
            $preserveRawState = $this->preserveFormDataWhenCreatingAnother($this->form->getRawState());
        }

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord())->saveRelationships();

            $this->callHook('afterCreate');

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

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->getRecord()::class);
            $this->record = null;

            $this->fillForm();

            $this->form->rawState([
                ...$this->form->getRawState(),
                ...$preserveRawState,
            ]);

            return;
        }

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preserveFormDataWhenCreatingAnother(array $data): array
    {
        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        $title = $this->getCreatedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($title);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->getCreatedNotificationMessage() ?? __('filament-panels::resources/pages/create-record.notifications.created.title');
    }

    /**
     * @deprecated Use `getCreatedNotificationTitle()` instead.
     */
    protected function getCreatedNotificationMessage(): ?string
    {
        return null;
    }

    public function createAnother(): void
    {
        $this->create(another: true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        if ($parentRecord = $this->getParentRecord()) {
            return $this->associateRecordWithParent($record, $parentRecord);
        }

        $record->save();

        return $record;
    }

    protected function associateRecordWithParent(Model $record, Model $parent): Model
    {
        return static::getResource()::getParentResourceRegistration()->getRelationship($parent)->save($record);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            ...($this->canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        $hasFormWrapper = $this->hasFormWrapper();

        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit($hasFormWrapper ? $this->getSubmitFormLivewireMethodName() : null)
            ->action($hasFormWrapper ? null : $this->getSubmitFormLivewireMethodName())
            ->keyBindings(['mod+s']);
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getCreateFormAction();
    }

    protected function getSubmitFormLivewireMethodName(): string
    {
        return 'create';
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create_another.label'))
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    protected function getCancelFormAction(): Action
    {
        $url = $this->previousUrl ?? $this->getResourceUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler(
                FilamentView::hasSpaMode($url)
                    ? 'document.referrer ? window.history.back() : Livewire.navigate(' . Js::from($url) . ')'
                    : 'document.referrer ? window.history.back() : (window.location.href = ' . Js::from($url) . ')',
            )
            ->color('gray');
    }

    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/create-record.title', [
            'label' => static::getResource()::getTitleCaseModelLabel(),
        ]);
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
                    ->operation('create')
                    ->model($this->getModel())
                    ->statePath($this->getFormStatePath()),
            ),
        ];
    }

    public function configureForm(Schema $schema): Schema
    {
        $schema->columns($this->hasInlineLabels() ? 1 : 2);
        $schema->inlineLabel($this->hasInlineLabels());

        static::getResource()::form($schema);

        $this->form($schema);

        return $schema;
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
            return $this->getResourceUrl('view', $this->getRedirectUrlParameters());
        }

        if ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
            return $this->getResourceUrl('edit', $this->getRedirectUrlParameters());
        }

        return $this->getResourceUrl();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRedirectUrlParameters(): array
    {
        return [];
    }

    /**
     * @return Model|class-string<Model>|null
     */
    protected function getMountedActionSchemaModel(): Model | string | null
    {
        return $this->getModel();
    }

    public function canCreateAnother(): bool
    {
        return static::$canCreateAnother;
    }

    public static function disableCreateAnother(): void
    {
        static::$canCreateAnother = false;
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...$this->getFormContentComponents(),
            ]);
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
            'fi-resource-create-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
