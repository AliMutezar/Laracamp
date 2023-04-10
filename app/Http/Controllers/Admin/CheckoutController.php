<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CheckoutController extends Controller
{
    public function update(Request $request, Checkout $checkout): RedirectResponse
    {
        $checkout->is_paid = true;
        $checkout->save();
        
        return Redirect::route('admin.dashboard')->with('success', "Checkout with ID {$checkout->id} has been updated");
    }
}
