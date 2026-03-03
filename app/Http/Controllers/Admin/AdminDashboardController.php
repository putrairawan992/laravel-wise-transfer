<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'transfers' => Transfer::count(),
            'success_transfers' => Transfer::where('status', 'success')->count(),
        ];

        // Prepare chart data for the last 7 days
        $chartData = [];
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $transfersByDate = Transfer::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        foreach ($dates as $date) {
            $data = $transfersByDate->get($date);
            $chartData[] = [
                'date' => \Carbon\Carbon::parse($date)->format('M d'),
                'count' => $data ? $data->count : 0,
                'total' => $data ? $data->total : 0,
            ];
        }

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}

