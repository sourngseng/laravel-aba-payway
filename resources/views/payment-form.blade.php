<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - ABA PayWay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .payment-info {
            margin-bottom: 20px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
            margin: 10px 0;
        }
        .transaction-id {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .submit-btn {
            background-color: #00b4d8;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #0096c7;
        }
        .info-text {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Redirecting to ABA PayWay</h2>
        
        <div class="payment-info">
            <div class="amount">{{ $paymentData['amount'] }} {{ $paymentData['currency'] }}</div>
            <div class="transaction-id">Transaction ID: {{ $paymentData['tran_id'] }}</div>
        </div>

        <div class="spinner"></div>
        
        <p>Please wait while we redirect you to ABA PayWay for secure payment processing...</p>
        
        <form id="payment-form" action="{{ config('aba-payway.checkout_url') }}/purchase" method="POST" style="display: none;">
            @foreach($paymentData as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            
            <button type="submit" class="submit-btn">
                Continue to ABA PayWay
            </button>
        </form>

        <div class="info-text">
            If you are not automatically redirected, please click the button below:
        </div>
        
        <button onclick="document.getElementById('payment-form').submit();" class="submit-btn" style="margin-top: 10px;">
            Continue to Payment
        </button>
    </div>

    <script>
        // Auto-submit the form after 3 seconds
        setTimeout(function() {
            document.getElementById('payment-form').submit();
        }, 3000);
    </script>
</body>
</html>