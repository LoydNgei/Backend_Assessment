<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Add a transaction to a wallet
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string'
        ]);

        $transaction = Transaction::create($data);
        return response()->json(['data' => $transaction], 201);
    }
}
