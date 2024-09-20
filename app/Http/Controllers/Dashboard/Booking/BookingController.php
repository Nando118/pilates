<?php

namespace App\Http\Controllers\Dashboard\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-dashboard")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        $title = "Delete Booking!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.bookings.index", [
            "title_page" => "Pilates | Bookings"
        ]);
    }

    public function getData()
    {
        $bookings = Booking::get();

        return DataTables::of($bookings)
            ->addColumn("lesson", function ($booking) {
                $lessonName = ucfirst($booking->lessonSchedule->lesson->name ?? "N/A");
                $lessonType = ucfirst($booking->lessonSchedule->lessonType->name ?? "N/A");
                $coachName = ucfirst($booking->lessonSchedule->user->name ?? "N/A");

                return "<strong>" . $lessonName . " / " . $lessonType . "</strong>" . "<br>" . $coachName;
            })
            ->addColumn("date", function ($booking) {
                $date = $booking->lessonSchedule->date ?? "N/A";
                $time = $booking->lessonSchedule->timeSlot->start_time ?? "N/A";

                return "<strong>" . $date . "</strong>" . "<br>" . $time;
            })
            ->addColumn("username", function ($booking) {
                $username = ucfirst($booking->user->name ?? "-");

                return $username;
            })
            ->addColumn("action", function ($booking) {
                $btn = '<div class="btn-group mr-1">';                
                $btn .= '<a href="#" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(["lesson", "date", "action"])
            ->make(true);
    }
}
