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
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Midtrans\Config;
use Midtrans\Snap;

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

        // snap redirect midtrans
        $this->getSnapRedirect($checkout);

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


    /**
     * Midtrans Handler
     */
    public function getSnapRedirect(Checkout $checkout)
    {
        $orderId = $checkout->id . '-' . Str::random(5);
        $price   = $checkout->Camps->price * 1000;
        $checkout->midtrans_booking_code = $orderId;

        $transaction_details = [
            "order_id"      =>  $orderId,
            "gross_amount"  =>  $price
        ];

        $item_details[] = [
            "order_id"      =>  $orderId,
            "price"         =>  $price,
            "quantity"      =>  1,
            "name"          =>  "Payment for {$checkout->Camps->title} camp"
        ];

        $userData = [
            "first_name"    =>  $checkout->User->name,
            "last_name"     =>  "MIDTRANSER",
            "address"       =>  $checkout->User->address,
            "city"          =>  "Jakarta Barat",
            "postal_code"   =>  "11550",
            "phone"         =>  $checkout->User->phone,
            "country_code"  =>  "IDN"
        ];

        $customer_details = [
            "first_name"    =>  $checkout->User->name,
            "last_name"     =>  "",
            "email"         =>  $checkout->User->email,
            "phone"         =>  $checkout->User->phone,
            "billing_address"  => $userData,
            "shipping_address" => $userData
        ];

        $midtrans_params = [
            "transaction_details"   => $transaction_details,
            "item_details"          => $item_details,
            "customer_details"      =>  $customer_details
        ];

        try {
            // Get snap payment URL
            $paymentUrl = Snap::createTransaction($midtrans_params)->redirect_url;
            $checkout->midtrans_url = $paymentUrl;
            $checkout->save();

            return $paymentUrl;

        } catch (Exception $e) {
            return false;
        }
    }
}
