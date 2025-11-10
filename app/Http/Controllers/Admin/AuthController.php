<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    function index()
    {
        return view('login');
    }
    function loginForm(Request $request)
    {
        $data = $request->only('email', 'password');
        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        } else {
            return redirect()->back()->with('gagal', 'Email atau Password anda salah');
        }
    }

    function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}