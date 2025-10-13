{{-- Example payment form --}}
{{-- Copy this to resources/views/payment/form.blade.php in your Laravel app --}}

@extends('layouts.app')

@section('title', 'Payment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Complete Your Payment') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('payment.process') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input id="amount" type="number" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           name="amount" 
                                           value="{{ old('amount') }}" 
                                           required 
                                           autocomplete="amount" 
                                           autofocus
                                           step="0.01"
                                           min="1">
                                </div>

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="customer_name" class="col-md-4 col-form-label text-md-end">{{ __('Full Name') }}</label>

                            <div class="col-md-6">
                                <input id="customer_name" type="text" 
                                       class="form-control @error('customer_name') is-invalid @enderror" 
                                       name="customer_name" 
                                       value="{{ old('customer_name') }}" 
                                       required 
                                       autocomplete="name">

                                @error('customer_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="customer_email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="customer_email" type="email" 
                                       class="form-control @error('customer_email') is-invalid @enderror" 
                                       name="customer_email" 
                                       value="{{ old('customer_email') }}" 
                                       required 
                                       autocomplete="email">

                                @error('customer_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="customer_phone" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                            <div class="col-md-6">
                                <input id="customer_phone" type="tel" 
                                       class="form-control @error('customer_phone') is-invalid @enderror" 
                                       name="customer_phone" 
                                       value="{{ old('customer_phone') }}" 
                                       autocomplete="tel"
                                       placeholder="+855 12 345 678">

                                @error('customer_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-end">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <input id="description" type="text" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       name="description" 
                                       value="{{ old('description') }}" 
                                       required
                                       placeholder="What are you paying for?">

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Proceed to Payment') }}
                                </button>
                                
                                <a class="btn btn-link" href="{{ url('/') }}">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Payment Information</h6>
                        <p class="card-text small text-muted">
                            Your payment will be processed securely through ABA PayWay. 
                            You will be redirected to ABA's secure payment portal to complete your transaction.
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">Secure</span>
                            <span class="text-muted small">256-bit SSL encryption</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection