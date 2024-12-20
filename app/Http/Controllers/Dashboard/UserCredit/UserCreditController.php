<?php

namespace App\Http\Controllers\Dashboard\UserCredit;

use App\Helpers\TransactionCodeHelper;
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

    /* public function getData()
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
                $btn .= '<a href="'. route('user-credits.edit', ["user" => $user->id]) .'" class="btn btn-info btn-sm" title="Manage User Credits"><i class="fas fa-fw fa-coins"></i></a> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    } */

    public function getData()
    {
        // Join tabel untuk menghindari N+1 problem
        $query = User::select('users.*', 'user_profiles.phone', 'user_profiles.gender')
        ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
        ->whereNotIn('roles.name', ['super_admin', 'admin', 'coach']);

        return DataTables::eloquent($query)
        ->addColumn('phone', function ($user) {
            return $user->phone ?? 'N/A';
        })
        ->addColumn('gender', function ($user) {
            return ucfirst($user->gender) ?? 'N/A';
        })
        ->addColumn('action', function ($user) {
            $btn = '<div class="btn-group mr-1">';
            $btn .= '<a href="' . route('user-credits.edit', ["user" => $user->id]) . '" class="btn btn-info btn-sm" title="Manage User Credits"><i class="fas fa-fw fa-coins"></i></a> ';
            $btn .= '</div>';
            return $btn;
        })
        ->rawColumns(['action'])
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
            $transactionType = $validated['type']; // add or subtract

            DB::beginTransaction();

            // Ambil data user
            $user = User::findOrFail($user->id);
            $oldCredits = $user->credit_balance;
            $creditChange = intval($validated["credit_balance"]);

            // Logika untuk menambah/mengurangi kredit
            if ($transactionType === "add") {
                $newCredits = $oldCredits + $creditChange;
            } elseif ($transactionType === "deduct") {
                // Pastikan kredit tidak negatif
                if ($oldCredits < $creditChange) {
                    alert()->error("Oops...", "The reduction in the credit amount cannot exceed the current credit amount.");
                    return redirect()->back();
                }
                $newCredits = $oldCredits - $creditChange;
            } else {
                alert()->error("Oops...", "Invalid transaction type. Please try again.");
                return redirect()->back();
            }

            // Update kredit pengguna
            $user->credit_balance = $newCredits;
            $user->save();

            // Tambahkan catatan transaksi
            CreditTransaction::query()->create([
                "user_id" => $user->id,
                "type" => $transactionType,
                "amount" => $creditChange,
                "transaction_code" => TransactionCodeHelper::generateTransactionCode(),                
                "description" => $creditChange . ' credits has been '. $transactionType .' by '. Auth::user()->email .' on the account ' . $user->email . '.'
            ]);

            DB::commit();

            alert()->success("Success!", ucfirst($transactionType) . "ed user credits successfully.");
            return redirect()->route("user-credits.index");
        } catch (\Exception $e) {
            Log::error("Error updating user credit in UserCreditController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oops...", "An error occurred while updating user credit data. Please try again.");
            return redirect()->back();
        }
    }
}
