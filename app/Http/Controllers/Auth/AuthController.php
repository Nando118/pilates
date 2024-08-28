<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterWithNormalFormRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login.login", [
            "title_page" => "Pilates | Sign In"
        ]);
    }

    public function register()
    {
        return view("auth.register.register", [
            "title_page" => "Pilates | Sign Up"
        ]);
    }

    public function registerWithNormalForm(RegisterWithNormalFormRequest $request) {
        $validated = $request->validated();
    }
}
