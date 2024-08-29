<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function user()
    {
        return view("user.welcome", [
            "title_page" => "Pilates | User"
        ]);
    }
    
    public function dashboard()
    {
        return view("dashboard.layout.main-layout", [
            "title_page" => "Pilates | Admin & Coach"
        ]);
    }
}
