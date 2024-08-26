<?php

namespace App\Http\Controllers;

use App\Models\TransactionWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function wallet_topups()
    {
        $topup_transactions = TransactionWallet::where('type', 'Topup')->orderByDesc('id')->paginate(10);

        return view('admin.wallet_transactions.topups', compact('topup_transactions'));
    }
    public function wallet_withdraws()
    {
        $topup_transactions = TransactionWallet::where('type', 'Withdraw')->orderByDesc('id')->paginate(10);

        return view('admin.wallet_transactions.withdrawals', compact('withdraw_transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionWallet $walletTransaction)
    {
        return view('admin.wallet_transactions.details', compact('walletTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionWallet $transactionWallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionWallet $walletTransaction)
    {
        $user_to_be_approved = User::where('id', $walletTransaction->user_id)->first();
        DB::transaction(function () use ($walletTransaction, $user_to_be_approved, $request) {
            if ($walletTransaction->type === 'Withdraw') {
                if ($request->hasFile('proof')) {
                    $proofPath = $request->file('proof')->store('proofs', 'public');
                }
                $user_to_be_approved->wallet->update([
                    'balance' => $user_to_be_approved->wallet->balance - $walletTransaction->amount
                ]);
                $walletTransaction->update([
                    'is_paid' => true,
                    'proof' => $proofPath
                ]);
            } else if ($walletTransaction->type === 'Topup') {
                $walletTransaction->update([
                    'is_paid' => true,
                ]);
                $user_to_be_approved->wallet->increment('balance', $walletTransaction->amount);
            }
        });
        if ($walletTransaction->type === 'Withdraw') {
            return redirect()->route('admin.withdraws');
        } else {
            return redirect()->route('admin.topups');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionWallet $transactionWallet)
    {
        //
    }
}
