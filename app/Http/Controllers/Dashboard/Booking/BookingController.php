<?php

namespace App\Http\Controllers\Dashboard\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies('access-dashboard')) {
                return abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view("dashboard.booking.index", [
            "title_page" => "Pilates | Booking"
        ]);
    }
}
