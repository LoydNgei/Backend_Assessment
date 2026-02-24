<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Create a new user account
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
        ]);

        return response()->json(['data' => $user], 201);
    }

    // Show a user's profile with all wallets, balances, and overall balance
    public function show(User $user): JsonResponse
    {
        $user->load('wallets.transactions');

        // Calculate each wallet's balance
        $wallets = $user->wallets->map(function ($wallet) {
            $income = $wallet->transactions->where('type', 'income')->sum('amount');
            $expense = $wallet->transactions->where('type', 'expense')->sum('amount');
            $wallet->balance = $income - $expense;
            return $wallet;
        });

        // Calculate overall balance across all wallets
        $totalBalance = $wallets->sum('balance');

        return response()->json([
            'data' => [
                'user' => $user,
                'wallets' => $wallets,
                'total_balance' => $totalBalance,
            ],
        ]);
    }
}
