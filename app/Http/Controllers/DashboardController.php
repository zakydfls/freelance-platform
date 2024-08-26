<?php

namespace App\Http\Controllers;

use App\Models\TransactionWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function wallet()
    {
        $user = Auth::user();

        // topup, withdrawal, revenue, expense
        $wallet_transaction = TransactionWallet::where("user_id", $user->id)
            ->orderBy('id')
            ->paginate(10);

        return view('dashboard.wallet', compact('wallet_transaction'));
    }
}
