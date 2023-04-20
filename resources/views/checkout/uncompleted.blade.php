@extends('layouts.app')

@section('content')
    
    <section class="checkout">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-12 col-12">
                    <img src="{{ asset('images/ill_register.png') }}" height="400" class="mb-5" alt=" ">
                </div>

                @if ($status)
                    <div class=" col-lg-12 col-12 header-wrap mt-4">
                        <p class="story">
                            Status saat ini {{ $status->transaction_status }}
                        </p>
                        <a href="{{ $status->midtrans_url }}" class="btn btn-primary mt-3">
                            Segera Selesaikan Pembayaran Anda
                        </a>
                    </div>
                @endif

            </div>

    </section>
@endsection