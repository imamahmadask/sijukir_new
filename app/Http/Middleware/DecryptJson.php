<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DecryptJson
{
    /**
     * Handle an incoming request.
     * 
     * Decrypts AES-256-CBC encrypted JSON payload from the vendor.
     * Expected request body: { "data": "<encrypted_base64_string>" }
     * 
     * The secret key is dynamically generated daily:
     * md5('QrisCobaDulu' + date('Ymd'))
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get encrypted data from request
        $encryptedData = $request->input('data');

        if (!$encryptedData) {
            return response()->json([
                'success' => false,
                'message' => 'Bad Request: No encrypted data provided.',
            ], 400);
        }

        // 2. Decrypt the data with daily-rotating key
        try {
            $today = Carbon::now()->format('Ymd');
            $secretKey = md5('QrisCobaDulu' . $today);

            $decoded = base64_decode($encryptedData);

            if ($decoded === false) {
                throw new \Exception('Invalid base64 encoding.');
            }

            // Extract IV (first 16 bytes) and ciphertext
            $iv = substr($decoded, 0, 16);
            $ciphertext = substr($decoded, 16);

            $decrypted = openssl_decrypt(
                $ciphertext,
                'AES-256-CBC',
                $secretKey,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                throw new \Exception('Decryption failed.');
            }

            $jsonData = json_decode($decrypted, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON after decryption.');
            }

            // 3. Merge decrypted data into the request
            $request->merge(['decrypted_payload' => $jsonData]);

        } catch (\Exception $e) {
            Log::error('Notification API: Decryption failed', [
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bad Request: Failed to decrypt data.',
            ], 400);
        }

        return $next($request);
    }
}
