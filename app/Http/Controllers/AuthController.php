<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function formlogin(){
        return view('auth.login');
    }

    public function login(Request $request){
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()
        ->withErrors(['username' => 'Username atau password salah'])
        ->onlyInput('username');
    }

    public function formgantipassword(){
        return view('auth.ganti-password');;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function gantiPassword(Request $request){
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back ()->withErrors(['password_lama' => 'Password lama salah']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('sukses', 'Password berhasil diganti');
    }

}
