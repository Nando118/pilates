<?php

namespace App\Http\Controllers\Dashboard\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
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
        $title = "Delete Room!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.rooms.index", [
            "title_page" => "Pilates | Rooms"
        ]);
    }

    public function getData()
    {
        $roomDatas = Room::get();

        return DataTables::of($roomDatas)
            ->addColumn("action", function ($room) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="' . route("rooms.edit", ["room" => $room->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("rooms.delete", ["room" => $room->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("rooms.store");

        return view("dashboard.rooms.form.form", [
            "title_page" => "Pilates | Add New Room",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(RoomRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            Room::create([
                "name" => $validated["name"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new room data.");
            return redirect()->route("rooms.index");
        } catch (\Exception $e) {
            Log::error("Error adding room in RoomController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new room data, please try again.");
            return redirect()->back();
        }
    }

    public function edit(Room $room)
    {
        $action = route("rooms.update", ["room" => $room->id]);

        return view("dashboard.rooms.form.form", compact("room", "action"))
        ->with([
            "title_page" => "Pilates | Update Room",
            "method" => "POST"
        ]);
    }

    public function update(Room $room, RoomRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $room = Room::findOrFail($room->id);
            $room->name = $validated["name"];
            $room->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated room data.");
            return redirect()->route("rooms.index");
        } catch (\Exception $e) {
            Log::error("Error updating room in RooomController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updated a room data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(Room $room)
    {
        try {
            DB::beginTransaction();

            $room = Room::findOrFail($room->id);

            $room->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted room data.");
            return redirect()->route("rooms.index");
        } catch (\Exception $e) {
            Log::error("Error deleting room in RoomController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted room data, please try again.");
            return redirect()->back();
        }
    }
}
