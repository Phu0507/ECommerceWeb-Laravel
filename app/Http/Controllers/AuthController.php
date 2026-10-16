<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(){
        return view('aadmin.authen.login');
    }

    public function postLogin(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended('dashboard')->with('success','Đăng nhập thành công');
        }
        return back()->withErrors([
            'password'=>'Thông tin đăng nhập không chính xác',
        ]);
    }

    public function logout(Request $request){
        Auth::guard('web')->logout(); 
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}