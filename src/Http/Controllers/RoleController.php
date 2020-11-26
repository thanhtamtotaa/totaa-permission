<?php

namespace Totaa\TotaaPermission\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Totaa\TotaaPermission\DataTables\AdminRoleDataTable;

class RoleController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index(AdminRoleDataTable $dataTable)
    {
        if (Auth::user()->bfo_info->hasAnyPermission(["view-role"])) {
            return $dataTable->render('totaa-permission::role', ['title' => 'Quản lý Role']);
        } else {
            return view('errors.dynamic', [
                'error_code' => '403',
                'error_description' => 'Không có quyền truy cập',
                'title' => 'Quản lý Role',
            ]);
        }
    }
}
