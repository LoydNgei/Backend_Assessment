<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Wallet;

class WalletController extends Controller
{
    // Create a wallet for a user
    public function store(Request $request): JsonResponse
    {
       $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'name' => 'required|string'
       ]);

       $wallet = Wallet::create($data);
       return response()->json(['data' => $wallet], 201);
    }

    // Show a single wallet with its balance and all transactions
    public function show(Wallet $wallet): JsonResponse
    {
        $wallet->load('transactions');

        $income = $wallet->transactions->where('type', 'income')->sum('amount');
        $expense = $wallet->transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        return response()->json([
            'data' => [
                'wallet' => $wallet,
                'balance' => $balance,
                'transactions' => $wallet->transactions,
            ],
        ]);
    }
}
