<script>

    //Ẩn toàn bộ modal
    window.addEventListener('hide_modal', function(e) {
        $("*").modal("hide");
    })

    //Hiện modal cụ thể
    window.addEventListener('show_modal', event => {
        $(event.detail).modal("show");
    })

    //Toastr thông báo
    window.addEventListener('toastr', event => {
        toastr[event.detail.type](event.detail.message, event.detail.title, {
            positionClass: "toast-top-right",
            closeButton: true,
            progressBar: true,
            timeOut: 15000,
            extendedTimeOut: 2000,
            preventDuplicates: false,
            newestOnTop: true,
            rtl: $("body").attr("dir") === "rtl" ||
                $("html").attr("dir") === "rtl"
        });
    })

    //Block UI khi ấn thêm mới
    Livewire.on('add_role', function() {
        Customize_BlockUI();
    });

    //Gọi view edit role
    $(document).on("click", "[customize-edit-role]", function() {
        Customize_BlockUI();
        Livewire.emit('edit_role', $(this).attr("customize-edit-role"));
    });

    //Gọi view set-role-permission
    $(document).on("click", "[customize-set-role-permission]", function() {
        Customize_BlockUI();
        Livewire.emit('set_role_permission', $(this).attr("customize-set-role-permission"));
    });

    //Gọi view xác nhận xóa
    $(document).on("click", "[customize-delete-role]", function() {
        Customize_BlockUI();
        Livewire.emit('delete_role', $(this).attr("customize-delete-role"));
    });

    //Xử lý khi dữ liệu đã được load xong
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.hook("message.processed", (message, component) => {
            $.unblockUI();

            if ($("select.select2-customize").length != 0) {
                $("select.select2-customize").each(function(e) {
                    $(this)
                        .wrap('<div class="position-relative"></div>')
                        .select2({
                            placeholder: $(this).attr("customize-placeholder"),
                            minimumResultsForSearch: $(this).attr("customize-search"),
                            dropdownParent: $("#" + $(this).attr("id") + "_div"),
                        })
                        .change(function(e) {
                            @this.set($(this).attr("wire:model"), $(this).val());
                        });
                });
            }

            if ($("input.datetimepicker-customize").length != 0) {
                $("input.datetimepicker-customize").each(function(e) {
                    $(this)
                        .datetimepicker({
                            format: "dd-mm-yyyy hh:ii",
                            autoclose: true,
                            todayBtn: true,
                            minuteStep: 15,
                            todayHighlight: true,
                            bootcssVer: 4,
                            zIndex: 3050,
                            language: "vi",
                            pickerPosition: "top-left",
                            weekStart: 1,
                        })
                        .change(function(e) {
                            @this.set($(this).attr("wire:model"), $(this).val());
                        });
                });
            }
        });
    });

</script>
