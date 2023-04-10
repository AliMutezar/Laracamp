<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Checkout $checkout)
    {
        $checkouts = Checkout::with('Camps')->get();
        // return $checkouts;
        return view('admin.dashboard', compact('checkouts'));
    }
}
