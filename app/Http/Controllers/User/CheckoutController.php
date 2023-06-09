<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Camps;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\Checkout\Store;
use App\Models\User;

use App\Mail\Checkout\AfterCheckout;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Camps $camps, Request $request)
    {
        // return $camps;

        if ($camps->isRegistered) {
            return redirect(route('user.dashboard'))->with('error', "You already registered on {$camps->title} camp.");
        }
        return view('checkout.create', compact('camps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request, Camps $camps, User $user)
    {

        // stop proses store
        // return $request->all();

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['camp_id'] = $camps->id;

        // Update user
        $user = User::find(Auth::user()->id);
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->occupation = $data['occupation'];
        $user->save();

        // Create checkout
        $checkout = Checkout::create($data);

        // sending email
        Mail::to(Auth::user()->email)->send(new AfterCheckout($checkout));

        return redirect(route('checkout.success'));


    }

    /**
     * Display the specified resource.
     */
    public function show(Checkout $checkout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checkout $checkout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checkout $checkout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checkout $checkout)
    {
        //
    }

    public function success()
    {
        return view('checkout.success');
    }
}
