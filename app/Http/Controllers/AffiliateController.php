<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function addUser(Request $request)
    {
        echo "test";
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'parent_id' => $validated['parent_id'],
        ]);

        return response()->json($user, 201);
    }

    public function recordSale(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $sale = Sale::create($validated);
        $this->distributeCommissions($sale);

        return response()->json($sale, 201);

    }

    private function distributeCommisions(Sale $sale) {
        $commissionRates = [10, 5, 3, 2, 1];
        $user = $sale->user;
        $level = 0;

        while($user && $level < 5) {
            $commissionAmount = ($commissionRates[$level]/100) * $sale->amount;

            Commission::create([
                'user_id' => $user->id,
                'sale_id' => $sale->id,
                'level' => $level + 1,
                'amount' => $commissionAmount,
            ]);

            $user = $user->parent;
            $level++;

        }
    }
}
