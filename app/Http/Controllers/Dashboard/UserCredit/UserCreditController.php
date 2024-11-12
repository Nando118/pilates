<?php

namespace App\Http\Controllers\Dashboard\UserCredit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UserCredits\UpdateUserCreditsRequest;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserCreditController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-dashboard") || Gate::denies("super-admin")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view("dashboard.user-credits.index", [
            "title_page" => "Ohana Pilates | User Credits"
        ]);
    }

    public function getData()
    {
        $users = User::with("profile")->whereDoesntHave("roles", function ($query) {
            $query->whereIn("name", ["super_admin", "admin", "coach"]);
        })->get();

        return DataTables::of($users)
            ->addColumn("phone", function ($user) {
                return ucfirst($user->profile->phone) ?? "N/A";
            })
            ->addColumn("gender", function ($user) {
                return ucfirst($user->profile->gender) ?? "N/A";
            })
            ->addColumn("action", function ($user) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. route('user-credits.edit', ["user" => $user->id]) .'" class="btn btn-success btn-sm" title="Add Credits to User"><i class="fas fa-fw fa-coins"></i></a> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function edit(User $user)
    {
        // Gunakan compact untuk merangkum variabel ke view
        $action = route("user-credits.update", $user->id);

        return view("dashboard.user-credits.form.form", compact("user", "action"))
        ->with([
            "title_page" => "Ohana Pilates | Add User Credits",
            "method" => "POST"
        ]);
    }

    public function update(User $user, UpdateUserCreditsRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Update data di tabel 'users'
            $user = User::findOrFail($user->id);
            $old_credits = $user->credit_balance;
            $new_credits = $old_credits + intval($validated["credit_balance"]);
            $user->credit_balance = $new_credits;
            $user->save();

            CreditTransaction::query()->create([
                "user_id" => $user->id,
                "type" => "add",
                "amount" => $validated["credit_balance"],
                "description" => 'Credit was added to account '. $user->email . ' by ' . Auth::user()->email . ', amounting to ' . $validated["credit_balance"] . ' credit.'
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added user credit.");
            return redirect()->route("user-credits.index");
        } catch (\Exception $e) {
            Log::error("Error updating user credit in UserCreditController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating user credit data, please try again.");
            return redirect()->back();
        }
    }
}
