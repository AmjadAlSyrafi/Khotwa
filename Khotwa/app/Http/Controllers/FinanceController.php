<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Get overall financial statistics (donations + expenses).
     */
    public function statistics(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        // Filters by date if provided
        $donationsQuery = Donation::query()->where('payment_status', 'paid');
        $expensesQuery  = Expense::query();

        if ($from) {
            $donationsQuery->whereDate('donated_at', '>=', $from);
            $expensesQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $donationsQuery->whereDate('donated_at', '<=', $to);
            $expensesQuery->whereDate('date', '<=', $to);
        }

        // Totals
        $totalDonations = $donationsQuery->sum('amount');
        $totalExpenses  = $expensesQuery->sum('amount');
        $balance        = $totalDonations - $totalExpenses;

        // Grouped by Project
        $byProject = Project::withSum(['donations' => function ($q) use ($from, $to) {
            $q->where('payment_status', 'paid');
            if ($from) $q->whereDate('donated_at', '>=', $from);
            if ($to)   $q->whereDate('donated_at', '<=', $to);
        }], 'amount')
        ->withSum(['expenses' => function ($q) use ($from, $to) {
            if ($from) $q->whereDate('date', '>=', $from);
            if ($to)   $q->whereDate('date', '<=', $to);
        }], 'amount')
        ->get()
        ->map(function ($project) {
            return [
                'project_id'   => $project->id,
                'project_name' => $project->name,
                'donations'    => $project->donations_sum_amount ?? 0,
                'expenses'     => $project->expenses_sum_amount ?? 0,
                'balance'      => ($project->donations_sum_amount ?? 0) - ($project->expenses_sum_amount ?? 0),
            ];
        });

        // Grouped by Event
        $byEvent = Event::withSum(['donations' => function ($q) use ($from, $to) {
            $q->where('payment_status', 'paid');
            if ($from) $q->whereDate('donated_at', '>=', $from);
            if ($to)   $q->whereDate('donated_at', '<=', $to);
        }], 'amount')
        ->withSum(['expenses' => function ($q) use ($from, $to) {
            if ($from) $q->whereDate('date', '>=', $from);
            if ($to)   $q->whereDate('date', '<=', $to);
        }], 'amount')
        ->get()
        ->map(function ($event) {
            return [
                'event_id'    => $event->id,
                'event_name'  => $event->title,
                'donations'   => $event->donations_sum_amount ?? 0,
                'expenses'    => $event->expenses_sum_amount ?? 0,
                'balance'     => ($event->donations_sum_amount ?? 0) - ($event->expenses_sum_amount ?? 0),
            ];
        });

        return ApiResponse::success([
            'totals' => [
                'donations' => $totalDonations,
                'expenses'  => $totalExpenses,
                'balance'   => $balance,
            ],
            'by_project' => $byProject,
            'by_event'   => $byEvent,
        ], 'Financial statistics calculated successfully.');
    }
}
