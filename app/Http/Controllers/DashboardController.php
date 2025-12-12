<?php

namespace App\Http\Controllers;
use App\Models\Dataset;
class DashboardController extends Controller
{

    public function index()
    {
        // Hitung total dataset
        $totalDataset = Dataset::count();

        return view('pages.dashboard.index', compact('totalDataset'));

    }

}