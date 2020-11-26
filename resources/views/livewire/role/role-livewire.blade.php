<div>
    <!-- Filters and Add Buttons -->
    @include('totaa-permission::livewire.role.sub.filters')

    <!-- Incluce các modal -->
    @include('totaa-permission::livewire.role.modal.add_edit')
    @include('totaa-permission::livewire.role.modal.delete_modal')
    @include('totaa-permission::livewire.role.modal.set_role_permission_modal')

    <!-- Scripts -->
    @push('livewires')
        @include('totaa-permission::livewire.role.sub.script')
    @endpush
</div>
