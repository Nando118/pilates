<?php

namespace App\Http\Controllers\Dashboard\CreditTransaction;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class CreditTransactionController extends Controller
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
        return view("dashboard.credit-transactions.index", [
            "title_page" => "Ohana Pilates | User Credits History"
        ]);
    }

    public function getData()
    {
        $credit_transactions = CreditTransaction::with(['user' => function ($query) {
            $query->withTrashed(); // Menyertakan pengguna yang dihapus soft
        }])->get();

        return DataTables::of($credit_transactions)
            ->addColumn("name", function ($credit_transaction) {
                // Memeriksa apakah pengguna ada, jika tidak ada maka tampilkan "N/A"
                return ucfirst($credit_transaction->user ? $credit_transaction->user->name : "N/A");
            })
            ->addColumn("type", function ($credit_transaction) {
                if ($credit_transaction->type === "add") {
                    return "<span class='badge badge-pill badge-success'><strong>" . ucfirst($credit_transaction->type) . "</strong></span>";
                } elseif ($credit_transaction->type === "deduct") {
                    return "<span class='badge badge-pill badge-danger'><strong>" . ucfirst($credit_transaction->type) . "</strong></span>";
                } elseif ($credit_transaction->type === "return") {
                    return "<span class='badge badge-pill badge-warning'><strong>" . ucfirst($credit_transaction->type) . "</strong></span>";
                } else {
                    return "<span class='badge badge-pill badge-info'><strong>not found</strong></span>";
                }
            })
            ->rawColumns(["type", "action"])
            ->make(true);
    }

}
