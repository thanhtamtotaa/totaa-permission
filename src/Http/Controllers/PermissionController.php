<?php

namespace Totaa\TotaaPermission\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Totaa\TotaaPermission\DataTables\AdminPermissionDataTable;

class PermissionController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index(AdminPermissionDataTable $dataTable)
    {
        if (Auth::user()->bfo_info->hasAnyPermission(["view-permission"])) {
            return $dataTable->render('totaa-permission::permission', ['title' => 'Quản lý Permission']);
        } else {
            return view('errors.dynamic', [
                'error_code' => '403',
                'error_description' => 'Không có quyền truy cập',
                'title' => 'Quản lý Permission',
            ]);
        }
    }
}
