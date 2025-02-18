@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <div
        wire:partial="action-modals"
        x-data="{
            actionNestingIndex: null,

            syncActionModals: function (newActionNestingIndex) {
                if (this.actionNestingIndex === newActionNestingIndex) {
                    return
                }

                if (this.actionNestingIndex !== null) {
                    this.closeModal()
                }

                this.actionNestingIndex = newActionNestingIndex

                if (this.actionNestingIndex === null) {
                    return
                }

                if (! this.$el.querySelector(`#${this.generateModalId(newActionNestingIndex)}`)) {
                    $nextTick(() => this.openModal())

                    return
                }

                this.openModal()
            },

            generateModalId: function (actionNestingIndex) {
                // HTML IDs must start with a letter, so if the Livewire component ID starts
                // with a number, we need to make sure it does not fail by prepending `fi-`.
                return 'fi-{{ $this->getId() }}-action-' + actionNestingIndex
            },

            openModal: function () {
                const id = this.generateModalId(this.actionNestingIndex)

                this.$dispatch('open-modal', { id })
            },

            closeModal: function () {
                const id = this.generateModalId(this.actionNestingIndex)

                this.$dispatch('close-modal-quietly', { id })
            },
        }"
        x-on:sync-action-modals.window="
            if ($event.detail.id === @js($this->getId())) syncActionModals($event.detail.newActionNestingIndex)
        "
        style="height: 0"
    >
        @foreach ($this->getMountedActions() as $action)
            @if ((! $loop->last) || $this->mountedActionShouldOpenModal())
                {{ $action->toModalHtmlable() }}
            @endif
        @endforeach
    </div>

    @php
        $this->hasActionsModalRendered = true;
    @endphp
@endif
