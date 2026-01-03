<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.panel.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'password' => ['required', 'string'],
        ]);

        // Tenant scope is automatically applied by TenantScoped trait
        // But we need to check if admin is active
        if (Auth::guard('web')->attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']])) {
            $admin = Auth::guard('web')->user();
            
            // Check if admin is active
            if ($admin && $admin->status !== 'active') {
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'phone' => 'حساب کاربری شما غیرفعال است.',
                ])->onlyInput('phone');
            }
            
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'phone' => 'شماره تماس یا رمز عبور اشتباه است.',
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }


    public function show()
    {
        $user = auth()->user();
        return view('admin.panel.auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|regex:/^09[0-9]{9}$/',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'پروفایل با موفقیت به‌روزرسانی شد ✅');
    }
}
