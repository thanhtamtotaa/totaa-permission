<?php

namespace Totaa\TotaaPermission\Http\Livewire;

use Livewire\Component;
use Auth;
use Spatie\Permission\Models\Role;

class RoleLivewire extends Component
{
    /**
     * Các biến sử dụng trong Component
     *
     * @var mixed
     */
    public $name, $description, $group, $order, $role_id;
    public $name_arrays, $modal_title, $permissions;

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
    protected $listeners = ['add_role', 'edit_role', 'delete_role', 'set_role_permission', ];

    /**
     * Validation rules
     *
     * @var array
     */
    protected function rules() {
        return [
            'name' => 'required|unique:roles,name,'.$this->role_id,
            'description' => 'required',
            'group' => 'nullable',
            'order' => 'required|numeric|min:0',
        ];
    }

    /**
     * Render view
     *
     * @return void
     */
    public function render()
    {
        return view('totaa-permission::livewire.role.role-livewire');
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
        if (!!!$this->role_id) {
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
     * add_role method
     *
     * @return void
     */
    public function add_role()
    {
        if (Auth::user()->bfo_info->cannot("add-role")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        $this->updateMode = true;
        $this->modal_title = "Thêm Role mới";
        $name_arrays = Role::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        $this->dispatchBrowserEvent('show_modal', "#add_edit_modal");
    }

    /**
     * edit_role method
     *
     * @return void
     */
    public function edit_role($id)
    {
        if (Auth::user()->bfo_info->cannot("edit-role")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            $this->reset();
            return null;
        }

        $this->role_id = $id;
        $role = Role::find($this->role_id);
        $this->name = $role->name;
        $this->description = $role->description;
        $this->group = $role->group;
        $this->order = $role->order;
        $this->updateMode = true;
        $this->modal_title = "Chỉnh sửa Role";
        $name_arrays = Role::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        if (!!$role->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Role đã khóa, không thể chính sửa"]);
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
        if (!Auth::user()->bfo_info->canAny(["add-role", "edit-role"])) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        //Xác thực dữ liệu
        if (!!$this->role_id) {
            $role = Role::find($this->role_id);

            if (!!$role->lock) {
                $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Role đã khóa, không thể chính sửa"]);
                return null;
            }
        }

        $this->validate();

        Role::updateOrCreate([
            "id" => $this->role_id,
        ], [
            'name' => mb_convert_case($this->vn_to_str(trim($this->name)), MB_CASE_LOWER, "UTF-8"),
            "description" => $this->description,
            "group" => $this->group,
            "order" => $this->order
        ]);

        //Đầy thông tin về trình duyệt
        $this->dispatchBrowserEvent('dt_draw');
        if (!!$this->role_id) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Chỉnh sửa Role thành công']);
        } else {
            $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Thêm Role mới thành công']);
        }

        $this->cancel();
    }

    /**
     * Hiện thị cửa sổ xác nhận xóa
     *
     * @return void
     */
    public function delete_role($id)
    {
        if (Auth::user()->bfo_info->cannot("delete-role")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            $this->reset();
            return null;
        }

        $this->role_id = $id;
        $role = Role::find($this->role_id);
        $this->name = $role->name;
        $this->description = $role->description;
        $this->group = $role->group;
        $this->order = $role->order;
        $name_arrays = Role::all()->pluck("group")->toArray();
        $this->name_arrays = array_filter(array_unique($name_arrays));

        if (!!$role->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Role đã khóa, không thể chính sửa"]);
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
        if (Auth::user()->bfo_info->cannot("delete-role")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        //Xóa
        $role = Role::find($this->role_id);

        if (!!$role->lock) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Role đã khóa, không thể chính sửa"]);
            return null;
        }

        $role->delete();

        //Đầy thông tin về trình duyệt
        $this->dispatchBrowserEvent('dt_draw');
        $this->cancel();
        $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Xóa thành công']);
    }


    /**
     * edit_role method
     *
     * @return void
     */
    public function set_role_permission($id)
    {
        if (Auth::user()->bfo_info->cannot("set-role-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            $this->reset();
            return null;
        }

        $this->role_id = $id;
        $role = Role::find($this->role_id);
        $this->description = $role->description;
        $this->group = $role->group;
        $this->updateMode = true;

        $this->permissions = $role->permissions()->pluck("name", "id")->toArray();

        $this->dispatchBrowserEvent('show_modal', "#set_role_permission_modal");
    }

    /**
     * edit_role method
     *
     * @return void
     */
    public function save_role_permission()
    {
        if (Auth::user()->bfo_info->cannot("set-role-permission")) {
            $this->dispatchBrowserEvent('toastr', ['type' => 'warning', 'title' => "Thất bại", 'message' => "Bạn không có quyền thực hiện hành động này"]);
            return null;
        }

        $role = Role::find($this->role_id);

        $role->syncPermissions($this->permissions);

        //Đầy thông tin về trình duyệt
        $this->dispatchBrowserEvent('dt_draw');
        $this->cancel();
        $this->dispatchBrowserEvent('toastr', ['type' => 'success', 'title' => "Thành công", 'message' => 'Lưu thông tin thành công']);
    }


    /**
     * vn_to_str
     *
     * @param  mixed $str
     * @return void
     */
    protected function vn_to_str($str=null){

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
