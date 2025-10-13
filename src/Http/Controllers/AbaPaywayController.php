<?php

namespace SourngSeng\AbaPayway\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use SourngSeng\AbaPayway\Facades\AbaPayway;
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;

class AbaPaywayController extends Controller
{
    /**
     * Handle payment return from ABA PayWay
     */
    public function handleReturn(Request $request): RedirectResponse
    {
        try {
            $transactionId = $request->input('tran_id');
            
            if (!$transactionId) {
                Log::error('ABA PayWay: No transaction ID in return request');
                return redirect()->route('payment.failed')->with('error', 'Invalid payment response');
            }

            $transaction = AbaPayway::getTransactionStatus($transactionId);
            
            if (!$transaction) {
                Log::error('ABA PayWay: Transaction not found', ['tran_id' => $transactionId]);
                return redirect()->route('payment.failed')->with('error', 'Transaction not found');
            }

            // Process the callback data if present
            if ($request->has(['status', 'amount', 'currency'])) {
                $callbackData = $request->only([
                    'tran_id', 'status', 'amount', 'currency', 'req_time', 'hash'
                ]);
                
                AbaPayway::processCallback($callbackData);
                
                // Refresh transaction data
                $transaction = $transaction->fresh();
            }

            if ($transaction->isSuccessful()) {
                return redirect()->route('payment.success', ['transaction' => $transaction->transaction_id])
                    ->with('success', 'Payment completed successfully');
            } elseif ($transaction->isPending()) {
                return redirect()->route('payment.pending', ['transaction' => $transaction->transaction_id])
                    ->with('info', 'Payment is being processed');
            } else {
                return redirect()->route('payment.failed', ['transaction' => $transaction->transaction_id])
                    ->with('error', 'Payment failed');
            }

        } catch (\Exception $e) {
            Log::error('ABA PayWay: Return handling error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return redirect()->route('payment.failed')->with('error', 'Payment processing error');
        }
    }

    /**
     * Handle webhook/callback from ABA PayWay
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $callbackData = $request->all();
            
            Log::info('ABA PayWay: Webhook received', $callbackData);

            if (AbaPayway::processCallback($callbackData)) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error'], 400);
            }

        } catch (\Exception $e) {
            Log::error('ABA PayWay: Webhook processing error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Get transaction status (API endpoint)
     */
    public function getTransactionStatus(Request $request, string $transactionId): JsonResponse
    {
        try {
            $transaction = AbaPayway::getTransactionStatus($transactionId);
            
            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'created_at' => $transaction->created_at,
                    'processed_at' => $transaction->processed_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ABA PayWay: Status check error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to retrieve transaction status'
            ], 500);
        }
    }

    /**
     * Show payment form
     */
    public function showPaymentForm(Request $request)
    {
        $paymentData = session('aba_payment_data');
        
        if (!$paymentData) {
            return redirect()->back()->with('error', 'Payment session expired');
        }

        return view('aba-payway::payment-form', compact('paymentData'));
    }

    /**
     * Create payment and redirect to ABA PayWay
     */
    public function createPayment(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'sometimes|string|in:USD,KHR',
                'email' => 'sometimes|email',
                'phone' => 'sometimes|string',
                'firstname' => 'sometimes|string',
                'lastname' => 'sometimes|string',
                'items' => 'sometimes|string',
                'metadata' => 'sometimes|array',
            ]);

            AbaPayway::validatePaymentData($validated);
            
            $paymentData = AbaPayway::createPayment($validated);
            
            // Store payment data in session for the form
            session(['aba_payment_data' => $paymentData]);
            
            return redirect()->route('aba-payway.payment-form');

        } catch (\Exception $e) {
            Log::error('ABA PayWay: Payment creation error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Payment creation failed: ' . $e->getMessage());
        }
    }
}