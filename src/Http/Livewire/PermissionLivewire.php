<?php

namespace Totaa\TotaaPermission\Http\Livewire;

use Livewire\Component;
use Auth;
use Spatie\Permission\Models\Permission;

class PermissionLivewire extends Component
{
    /**
     * Các biến sử dụng trong Component
     *
     * @var mixed
     */
    public $name, $description, $group, $order, $permission_id = NULL;
    public $name_arrays, $modal_title;

    /**
     * Cho phép cập nhật updateMode
     *
     * @var bool
     */
    public $updateMode = false;

    /**
     * Các biển sự kiện
     *
     * @var array
     */
    protected $listeners = ['add_permission', 'edit_permission', 'delete_permission', ];

    /**
     * Validation rules
     *
     * @var array
     */
    public function rules() {
        return [
            'name' => 'required|unique:permissions,name,'.$this->permission_id,
            'description' => 'required',
            'group' => 'nullable',
            'order' => 'required|numeric|min:0',
        ];
    }

    public function render()
    {
        return view('totaa-permission::livewire.permission.permission-livewire');
    }

    /**
     * On updated action
     *
     * @param  mixed $propertyName
     * @return void
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * updatedDescription
     *
     * @return void
     */
    public function updatedDescription()
    {
        if (!!!$this->permission_id) {
            $this->name = mb_convert_case($this->vn_to_str(trim($this->description)), MB_CASE_LOWER, "UTF-8");
        }
    }

    /**
     * cancel
     *
     * @return void
     */
    public function cancel()
    {
        $this->updateMode = false;
        $this->resetValidation();
        $this->reset();
        $this->dispatchBrowserEvent('hide_modal');
    }

    /**
     * add_permission method
     *
     * @return void
     */
    public function add_permission()
    {
        if (Auth::user()->bfo_info->cannot("add-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        $this->updateMode = true;
        $this->modal_title = "Thêm Permission mới";
        $name_arrays = Permission::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        $this->dispatchBrowserEvent('show_modal', "#add_edit_modal");
    }

    /**
     * edit_permission method
     *
     * @return void
     */
    public function edit_permission($id)
    {
        if (Auth::user()->bfo_info->cannot("edit-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            $this->reset();
            return null;
        }

        $this->permission_id = $id;
        $permission = Permission::find($this->permission_id);
        $this->name = $permission->name;
        $this->description = $permission->description;
        $this->group = $permission->group;
        $this->order = $permission->order;
        $this->updateMode = true;
        $this->modal_title = "Chỉnh sửa Permission";
        $name_arrays = Permission::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        if (!!$permission->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Permission đã khóa, không thể chính sửa"]);
            $this->reset();
            return null;
        }

        $this->dispatchBrowserEvent('show_modal', "#add_edit_modal");
    }

    /**
     * Lưu lại thông tin
     *
     * @return void
     */
    public function save()
    {
        if (!Auth::user()->bfo_info->canAny(["add-permission", "edit-permission"])) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        //Xác thực dữ liệu
        if (!!$this->permission_id) {
            $permission = Permission::find($this->permission_id);

            if (!!$permission->lock) {
                $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Permission đã khóa, không thể chính sửa"]);
                return null;
            }
        }

        $this->validate();

        Permission::updateOrCreate([
            "id" => $this->permission_id,
        ], [
            'name' => mb_convert_case($this->vn_to_str(trim($this->name)), MB_CASE_LOWER, "UTF-8"),
            "description" => $this->description,
            "group" => $this->group,
            "order" => $this->order
        ]);

        //Đầy thông tin về trình duyệt
        $this->dispatchBrowserEvent('dt_draw');
        if (!!$this->permission_id) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Chỉnh sửa Permission thành công']);
        } else {
            $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Thêm Permission mới thành công']);
        }

        $this->cancel();
    }

    /**
     * Hiện thị cửa sổ xác nhận xóa
     *
     * @return void
     */
    public function delete_permission($id)
    {
        if (Auth::user()->bfo_info->cannot("delete-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            $this->reset();
            return null;
        }

        $this->permission_id = $id;
        $permission = Permission::find($this->permission_id);
        $this->name = $permission->name;
        $this->description = $permission->description;
        $this->group = $permission->group;
        $this->order = $permission->order;
        $name_arrays = Permission::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        if (!!$permission->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Permission đã khóa, không thể chính sửa"]);
            $this->reset();
            return null;
        }

        $this->dispatchBrowserEvent('show_modal', "#delete_modal");
    }

    /**
     * Tiến hành xóa thôi
     *
     * @return void
     */
    public function delete()
    {
        if (Auth::user()->bfo_info->cannot("delete-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        //Xóa
        $permission = Permission::find($this->permission_id);

        if (!!$permission->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Permission đã khóa, không thể chính sửa"]);
            return null;
        }

        $permission->delete();

        //Đầy thông tin về trình duyệt
        $this->dispatchBrowserEvent('dt_draw');
        $this->cancel();
        $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Xóa thành công']);
    }


    /**
     * vn_to_str
     *
     * @param  mixed $str
     * @return void
     */
    protected function vn_to_str($str=null) {

        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(' ','-',$str);

        return $str;
    }
}
