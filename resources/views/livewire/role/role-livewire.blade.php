<div>
    <!-- Filters and Add Buttons -->
    @include('livewire.admin.role.sub.filters')

    <!-- Incluce các modal -->
    @include('livewire.admin.role.modal.add_edit')
    @include('livewire.admin.role.modal.delete_modal')
    @include('livewire.admin.role.modal.set_role_permission_modal')

    <!-- Scripts -->
    @push('livewires')
        @include('livewire.admin.role.sub.script')
    @endpush
</div>
