<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::query()
        ->latest()
        ->paginate(1);
        return view('admin.panel.admin',compact('admins'));
    }
}
