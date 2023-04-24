@extends('layouts.app')

@section('content')
    
    <section class="checkout">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-12 col-12">
                    <img src="{{ asset('images/ill_register.png') }}" height="400" class="mb-5" alt=" ">
                </div>

                @foreach ($status->Checkouts as $checkout)
                    @if ($checkout->transaction_status == 'pending')
                        <div class=" col-lg-12 col-12 header-wrap mt-4">
                            <p class="story">
                               MENUNGGU PEMBAYARAN
                            </p>
                            <a href="{{ route('checkout.snapredirect', $checkout->id) }}" class="btn btn-primary mt-3">
                                Pay with bank transfer
                            </a>
                            <a href="{{ route('checkout.gopay', $checkout->id) }}" class="btn btn-primary mt-3">
                                Pay with gopay
                            </a>
                        </div>
                    @endif
                @endforeach

            </div>

    </section>
@endsection