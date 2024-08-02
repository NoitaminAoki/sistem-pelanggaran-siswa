<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function viewLogin()
    {
        return view('auth.student-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::guard('studentUser')->attempt($credentials)) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('student.login')->withErrors(['plain' => "Incorrect username or password."]);
    }

    public function logout()
    {
        Auth::guard('studentUser')->logout();
        return redirect()->route('student.login');
    }
}
