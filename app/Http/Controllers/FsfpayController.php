<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // or use Guzzle directly
use Illuminate\Support\Facades\Log;

class FsfpayController extends Controller
{
    // Renders the page that contains the JS which will request iframe src
    public function checkout(Request $request)
    {
        $lang = $request->get('lang', app()->getLocale());
        return view('fsfpay-checkout', compact('lang'));
    }

    // Server-side: build payload, call FSFPAY API, return iframe url/token
    public function create(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'order_id' => 'nullable|string',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'lang' => 'nullable|string',
        ]);

        $payload = [
            'api_username' => config('fsfpay.api_username'),
            'api_key' => config('fsfpay.api_key'),
            'amount' => number_format($data['amount'], 2, '.', ''),
            'order_id' => $data['order_id'] ?? uniqid('order_'),
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'lang' => $data['lang'] ?? app()->getLocale(),
            'callback_url' => config('fsfpay.callback_url') ?? route('fsfpay.callback'),
        ];

        // Signature example (HMAC sha256) — Change according to FSFPAY document
        $signature = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES), config('fsfpay.secret_key'));
        $payload['signature'] = $signature;

        // Call FSFPAY API (example)
        $resp = Http::acceptJson()->post(config('fsfpay.api_url'), $payload);

        if (!$resp->ok()) {
            Log::error('FSFPAY create failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return response()->json(['error' => 'Payment provider error'], 500);
        }

        $body = $resp->json();

        // === ADAPT THIS PART to actual FSFPAY response ===
        // Common patterns:
        // - API returns {'token': '...', 'iframe_url': 'https://...'} OR
        // - API returns an order_id which you append to iframe base url
        //
        // Example A (iframe_url returned directly):
        if (!empty($body['iframe_url'])) {
            $iframeSrc = $body['iframe_url'];
        } elseif (!empty($body['token'])) {
            // Example B (iframe base + token)
            $iframeSrc = rtrim(config('fsfpay.iframe_url'), '/') . '?token=' . urlencode($body['token']);
        } elseif (!empty($body['order_id'])) {
            // Example C (order_id)
            $iframeSrc = rtrim(config('fsfpay.iframe_url'), '/') . '?order_id=' . urlencode($body['order_id']);
        } else {
            Log::error('FSFPAY create: unexpected response', $body);
            return response()->json(['error' => 'Invalid provider response'], 500);
        }

        return response()->json(['iframe_src' => $iframeSrc]);
    }

    // Callback webhook from FSFPAY: verify HMAC and handle status
    public function callback(Request $request)
    {
        $secret = config('fsfpay.secret_key');
        $payload = $request->getContent();
        $sigHeader = $request->header('X-FSFPay-Signature') ?? $request->header('X-Signature') ?? '';

        $calculated = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($calculated, $sigHeader)) {
            Log::warning('Invalid FSFPAY callback signature', ['calc' => $calculated, 'rec' => $sigHeader]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data = $request->json()->all();
        // handle payment: save to DB, update order, notify merchant/shopify, etc.
        Log::info('FSFPAY callback verified', $data);

        return response()->json(['message' => 'ok']);
    }
}

