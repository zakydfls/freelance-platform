<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopupWalletRequest;
use App\Http\Requests\StoreWithdrawWalletRequest;
use App\Models\TransactionWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function wallet()
    {
        $user = Auth::user();

        $wallet_transaction = TransactionWallet::where("user_id", $user->id)
            ->orderBy('id')
            ->paginate(10);

        return view('dashboard.wallet', compact('wallet_transaction'));
    }

    public function topup_wallet()
    {
        return view('dashboard.topup_wallet');
    }
    public function withdraw_wallet()
    {
        return view('dashboard.withdraw_wallet');
    }
    public function withdraw_wallet_store(StoreWithdrawWalletRequest $request)
    {
        $user = Auth::user();

        if ($user->wallet->balance < $request->amount || $user->wallet->balance < 50000) {
            return redirect()->back()->withErrors([
                'amount' => 'Your balance is not enough'
            ]);
        }

        DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }
            $validated['type'] = 'Withdraw';
            $validated['amount'] = $request->amount;
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newWithdrawWallet = TransactionWallet::create($validated);
            return redirect()->route('dashboard.wallet');
        });
    }
    public function topup_wallet_store(StoreTopupWalletRequest $request)
    {
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }
            $validated['type'] = 'Topup';
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newTopupWallet = TransactionWallet::create($validated);
        });
        return redirect()->route('dashboard.wallet');
    }
}
