<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Camps;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\Checkout\Store;
use App\Models\User;
use GuzzleHttp\Client;

// use App\Mail\Checkout\AfterCheckout;
// use Illuminate\Support\Facades\Mail;

use Exception;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{

    public function __construct()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVERKEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS');
    }

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
        $data['user_id'] = Auth::user()->id;
        $data['camp_id'] = $camps->id;
        $data['transaction_status'] = 'pending';

        // Update user
        $user = User::find(Auth::user()->id);
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->occupation = $data['occupation'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->save();

        // Create checkout
        Checkout::create($data);

        // sending email
        // Mail::to(Auth::user()->email)->send(new AfterCheckout($checkout));

        return redirect(route('checkout.process', $camps->id));


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

    public function process(Camps $camps, $id)
    {
        $status = Camps::with('Checkouts')->findOrFail($id);
        // return $status;
        return view('checkout.unfinish', [
            'status' => $status
        ]);
    }


    /**
     * Midtrans Handler
     */
    public function getSnapRedirect(Checkout $checkout, $id)
    {
        $data = Checkout::with(['Camps', 'User'])->findOrFail($id);
        // return $data;
        $orderId = $data->id.'-'.Str::random(5);
        $price = $data->Camps->price * 100;

        $transaction_details = [
            'order_id' => $orderId,
            'gross_amount' => $price
        ];

        $item_details[] = [
            'id' => $orderId,
            'price' => $price,
            'quantity' => 1,
            'name' => "Payment for {$data->Camps->title} Camp"
        ];

        $userData = [
            "first_name" => $data->User->name,
            "last_name" => "bin Fulan",
            "address" => $data->User->address,
            "city" => "Jakarta Barat",
            "postal_code" => "11550",
            "phone" => $data->User->phone,
            "country_code" => "IDN",
        ];

        $customer_details = [
            "first_name" => $data->User->name,
            "last_name" => "bin Fulan",
            "email" => $data->User->email,
            "phone" => $data->User->phone,
            "billing_address" => $userData,
            "shipping_address" => $userData,
        ];

        $enabledPayments  = ['bank_transfer', 'gopay'];
        $midtrans_params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'enabled_payments' => $enabledPayments,
            'gopay' =>  [
                'enable_callback' => true,
                'callback_url' => 'https://alimutezar.com/payment/finish'
            ]
        ];

        // dd($midtrans_params);
        try {
            // Get Snap Payment Page URL
            $paymentUrl = \Midtrans\Snap::createTransaction($midtrans_params)->redirect_url;
            $checkout = Checkout::find($id);
            $checkout->midtrans_url = $paymentUrl;
            $checkout->midtrans_order_id = $orderId;
            $checkout->save();

            return redirect($paymentUrl);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function gopayment(Checkout $checkout, $id)
    {
        $data = Checkout::with(['Camps', 'User'])->findOrFail($id);
        // return $data;
        $orderId = $data->id.'-'. Str::random(5);
        $price = $data->Camps->price * 100;

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1VUnVGRWhCemJwSGZWVm15cEctaGhlcnk6'   
        ];

        $userData = [
            "first_name" => $data->User->name,
            "last_name" => "bin Fulan",
            "address" => $data->User->address,
            "city" => "Jakarta Barat",
            "postal_code" => "11550",
            "phone" => $data->User->phone,
            "country_code" => "IDN",
        ];


        $gopayParams = [
            'transaction_details'   => [
                'order_id' =>  $orderId,
                'gross_amount' =>  $price
            ],

            'item_details' => [
                'id' => $data->Camps->id,
                'price' => $data->Camps->price * 100,
                'quantity' => 1,
                'name' => $data->Camps->title
            ],

            'customer_details'  => [
                'first_name' => $data->User->name,
                'last_name' => 'bin Fulan',
                'email' => $data->User->email,
                'phone' => $data->User->phone,
                "billing_address" => $userData,
                "shipping_address" => $userData
            ],

            'payment_type'  => 'gopay',
            'gopay' =>  [
                'enable_callback' => true,
                'callback_url' => 'https://alimutezar.com/payment/finish'
            ]
        ];

        // return $gopayParams;

        // Sending request body to Midtrans
        $client = new Client();
        $response = $client->post('https://api.sandbox.midtrans.com/v2/charge', [
            'headers'   =>  $headers,
            'json'      =>  $gopayParams
        ]);

        $responseBody = json_decode($response->getBody(), true);
        // return $responseBody;

        
        try {
            $deeplinkUrl = $responseBody['actions'][1]['url'];
            $qrcodeUrl = $responseBody['actions'][0]['url'];
            $checkout = Checkout::find($id);

            // Check browser viewport to redirect
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false || strpos($userAgent, 'iPhone') !== false ) {
                $checkout->midtrans_url = $deeplinkUrl;
                $checkout->midtrans_order_id = $orderId;
                $checkout->save(); 
                return redirect($deeplinkUrl);

            } else {
                $checkout->midtrans_url = $qrcodeUrl;
                $checkout->midtrans_order_id = $orderId;
                $checkout->save(); 
                return redirect($qrcodeUrl);
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function midtransCallback(Request $request)
    {
        $notif =  new \Midtrans\Notification();
        $orderId = explode('-', $notif->order_id);
        $order_id = $orderId[0];

        $status = $notif->transaction_status;
        $fraud = $notif->fraud_status;
        $type = $notif->payment_type;
        $checkout = Checkout::find($order_id);

        if ($status == 'capture') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'challenge'
                $checkout->transaction_status = 'pending';
                $checkout->midtrans_payment_type = $type;
            }
            else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'success'
                $checkout->transaction_status = 'paid';
                $checkout->midtrans_payment_type = $type;
            }
        }

        else if ($status == 'cancel') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->transaction_status = 'failed';
                $checkout->midtrans_payment_type = $type;
            }
            else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->transaction_status = 'failed';
                $checkout->midtrans_payment_type = $type;
            }
        }

        else if ($status == 'deny') {
            // TODO Set payment status in merchant's database to 'failure'
            $checkout->transaction_status = 'failed';
            $checkout->midtrans_payment_type = $type;
        }

        else if ($status == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $checkout->transaction_status = 'paid';
            $checkout->midtrans_payment_type = $type;
        }

        else if ($status == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $checkout->transaction_status = 'pending';
            $checkout->midtrans_payment_type = $type;
        }

        else if ($status == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $checkout->transaction_status = 'failed';
            $checkout->midtrans_payment_type = $type;
        }

        $checkout->save();
        return view('checkout/success');
    }

    public function midtransFinish(Request $request)
    {
        $transaction_status = $request->input('transaction_status');
        $orderId = explode('-', $request->input('order_id'));
        $order_id = $orderId[0];

        $status = Checkout::find($order_id);
        // return $status;
        if ($transaction_status == 'pending') {
            return view('checkout.uncompleted', [
                'status'    =>  $status
            ]);
        }
        return view('checkout.success');
    }

    // public function midtransUnfinish(Request $request)
    // {
    //     $orderId = explode('-', $request->input('order_id'));
    //     $order_id = $orderId[0];

    //     $status = Checkout::find($order_id);
    //     return view('checkout.uncompleted', [
    //         'status'    =>  $status
    //     ]);
    // }

    public function midtransError(Request $request)
    {
        return view('checkout.error');
    }
}