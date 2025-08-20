<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;

class ExpenseController extends Controller
{
    /**
     * List all expenses (with optional filters).
     */
    public function index()
    {
        $expenses = Expense::with(['project', 'event'])
            ->latest()
            ->get();

        return ApiResponse::success(ExpenseResource::collection($expenses), 'Expenses fetched successfully.');
    }

    /**
     * Store a new expense.
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create($request->validated());

        return ApiResponse::success(new ExpenseResource($expense), 'Expense created successfully.', 201);
    }

    /**
     * Show a single expense.
     */
    public function show($id)
    {
        $expense = Expense::with(['project', 'event'])->find($id);
        if (!$expense) {
            return ApiResponse::error('Expense not found.', 404);
        }
        return ApiResponse::success(new ExpenseResource($expense));
    }

    /**
     * Update an expense.
     */
    public function update(UpdateExpenseRequest $request, $id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return ApiResponse::error('Expense not found.', 404);
        }
        $expense->update($request->validated());

        return ApiResponse::success(new ExpenseResource($expense), 'Expense updated successfully.');
    }

    /**
     * Delete an expense.
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return ApiResponse::error('Expense not found.', 404);
        }
        $expense->delete();

        return ApiResponse::success(null, 'Expense deleted successfully.');
    }
}
