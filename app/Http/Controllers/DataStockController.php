<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataStockController extends Controller
{
    public function index()
    {
        return view('data-stock.index');
    }
}
