<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::where('owner_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json(['data' => $expenses]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:5120'
        ]);

        $data = $request->only(['amount', 'category', 'date', 'description']);
        $data['owner_id'] = $request->user()->id;

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image_path'] = $request->file('receipt_image')->store('expenses/receipts');
        }

        $expense = Expense::create($data);

        return response()->json([
            'message' => 'Expense logged successfully.',
            'data' => $expense
        ], 201);
    }
}
