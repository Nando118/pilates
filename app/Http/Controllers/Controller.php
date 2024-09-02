<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function home()
    {
        return view("home.welcome", [
            "title_page" => "Pilates | User"
        ]);
    }

    public function dashboard()
    {
        return view("dashboard.layouts.main-layout", [
            "title_page" => "Pilates | Admin & Coach"
        ]);
    }
}
