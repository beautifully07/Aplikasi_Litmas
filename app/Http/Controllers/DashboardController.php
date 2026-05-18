<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PBDewasa;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLitmas = PBDewasa::count();
        $totalKlien  = Client::count();

        return view('dashboard', compact(
            'totalLitmas',
            'totalKlien'
        ));
    }
}