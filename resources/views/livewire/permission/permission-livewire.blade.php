<div>
    <!-- Filters and Add Buttons -->
    @include('totaa-permission::livewire.permission.sub.filters')

    <!-- Incluce các modal -->
    @include('totaa-permission::livewire.permission.modal.add_edit')
    @include('totaa-permission::livewire.permission.modal.delete_modal')

    <!-- Scripts -->
    @push('livewires')
        @include('totaa-permission::livewire.permission.sub.script')
    @endpush
</div>
