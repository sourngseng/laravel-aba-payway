<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use SourngSeng\AbaPayway\Facades\AbaPayway;
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;
use SourngSeng\AbaPayway\Exceptions\AbaPaywayException;

/**
 * Example controller showing how to integrate ABA PayWay
 * This file should be copied to your Laravel application
 */
class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function showPaymentForm(): View
    {
        return view('payment.form');
    }

    /**
     * Process payment request
     */
    public function processPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string',
            'description' => 'required|string|max:255',
        ]);

        try {
            // Prepare payment data
            $paymentData = [
                'amount' => $validated['amount'],
                'currency' => 'USD',
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone'] ?? '',
                'firstname' => explode(' ', $validated['customer_name'])[0] ?? '',
                'lastname' => explode(' ', $validated['customer_name'], 2)[1] ?? '',
                'items' => $validated['description'],
                'metadata' => [
                    'order_id' => 'ORD_' . time(),
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                ]
            ];

            // Validate payment data
            AbaPayway::validatePaymentData($paymentData);

            // Create payment
            $payment = AbaPayway::createPayment($paymentData);

            // Store additional order information in your database
            // Order::create([...]);

            // Redirect to ABA PayWay payment form
            return redirect()->route('aba-payway.payment-form')
                ->with('success', 'Payment created successfully');

        } catch (AbaPaywayException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(Request $request): View
    {
        $transactionId = $request->get('transaction');
        $transaction = null;

        if ($transactionId) {
            $transaction = AbaPayway::getTransactionStatus($transactionId);
        }

        return view('payment.success', compact('transaction'));
    }

    /**
     * Payment cancelled page
     */
    public function paymentCancelled(): View
    {
        return view('payment.cancelled');
    }

    /**
     * Payment pending page
     */
    public function paymentPending(Request $request): View
    {
        $transactionId = $request->get('transaction');
        $transaction = null;

        if ($transactionId) {
            $transaction = AbaPayway::getTransactionStatus($transactionId);
        }

        return view('payment.pending', compact('transaction'));
    }

    /**
     * Payment failed page
     */
    public function paymentFailed(Request $request): View
    {
        $transactionId = $request->get('transaction');
        $transaction = null;

        if ($transactionId) {
            $transaction = AbaPayway::getTransactionStatus($transactionId);
        }

        return view('payment.failed', compact('transaction'));
    }

    /**
     * Admin: View all transactions
     */
    public function adminTransactions(): View
    {
        $transactions = AbaPaywayTransaction::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.transactions', compact('transactions'));
    }

    /**
     * Admin: View transaction details
     */
    public function adminTransactionDetails(string $transactionId): View
    {
        $transaction = AbaPaywayTransaction::where('transaction_id', $transactionId)
            ->firstOrFail();

        return view('admin.transaction-details', compact('transaction'));
    }

    /**
     * API: Check transaction status
     */
    public function apiTransactionStatus(string $transactionId)
    {
        $transaction = AbaPayway::getTransactionStatus($transactionId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'customer_email' => $transaction->customer_email,
                'created_at' => $transaction->created_at->toISOString(),
                'processed_at' => $transaction->processed_at?->toISOString(),
            ]
        ]);
    }

    /**
     * Handle successful payment (called after webhook processing)
     */
    protected function handleSuccessfulPayment(AbaPaywayTransaction $transaction): void
    {
        // Update your order status
        // Order::where('transaction_id', $transaction->transaction_id)
        //     ->update(['status' => 'paid']);

        // Send confirmation email
        // Mail::to($transaction->customer_email)
        //     ->send(new PaymentConfirmation($transaction));

        // Log the successful payment
        logger('Payment successful', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'customer_email' => $transaction->customer_email,
        ]);
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment(AbaPaywayTransaction $transaction): void
    {
        // Update your order status
        // Order::where('transaction_id', $transaction->transaction_id)
        //     ->update(['status' => 'failed']);

        // Log the failed payment
        logger('Payment failed', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'customer_email' => $transaction->customer_email,
        ]);
    }
}