<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Get all expenses for the authenticated landlord.
     *
     * HIGH-2 FIX: Guard both index() and store() to landlords only.
     * Without this check, tenants could view and log expenses — a landlord-only feature.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // HIGH-2 FIX: Tenants have no expenses to view.
        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Only landlords can view expenses.'], 403);
        }

        $expenses = Expense::where('owner_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20); // HIGH-6 FIX: Paginate instead of unbounded ->get()

        return response()->json(['data' => $expenses]);
    }

    /**
     * Log a new expense for the authenticated landlord.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // HIGH-2 FIX: Only landlords can log expenses.
        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Only landlords can log expenses.'], 403);
        }

        $validated = $request->validate([
            'amount'        => 'required|numeric|min:0',
            'category'      => 'required|string|max:100',
            'date'          => 'required|date',
            'description'   => 'nullable|string|max:500',
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'amount'      => $validated['amount'],
            'category'    => $validated['category'],
            'date'        => $validated['date'],
            'description' => $validated['description'] ?? null,
            'owner_id'    => $user->id,
        ];

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image_path'] = $request->file('receipt_image')->store('expenses/receipts');
        }

        $expense = Expense::create($data);

        return response()->json([
            'message' => 'Expense logged successfully.',
            'data'    => $expense,
        ], 201);
    }
}
