<?php

namespace App\Http\Controllers\Home\MyTransaction;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MyTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-user-home")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Default filter: today for date, and all types (null for all types)
        $selectedDate = $request->query("date", Carbon::today()->toDateString());
        $selectedType = $request->query("type", null);

        $myTransactionsQuery = CreditTransaction::where("user_id", Auth::user()->id)
            ->orderBy("created_at", "desc");

        // Filter by selected date
        if ($selectedDate) {
            $myTransactionsQuery->whereDate("created_at", Carbon::parse($selectedDate));
        }

        // Filter by selected type if not "All"
        if ($selectedType && in_array($selectedType, ["add", "deduct", "return"])) {
            $myTransactionsQuery->where("type", $selectedType);
        }

        $myTransactions = $myTransactionsQuery->paginate(10);

        return view("home.my-transactions.index", [
            "title_page" => "Ohana Pilates | My Transactions",
            "myTransactions" => $myTransactions,
            "selectedDate" => $selectedDate,
            "selectedType" => $selectedType,
        ]);
    }
}
