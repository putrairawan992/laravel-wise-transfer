<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $account = $user->accounts()->first();
        $transfers = $user->transfers()->latest()->limit(5)->get();

        return view('dashboard', compact('account', 'transfers'));
    }
}
