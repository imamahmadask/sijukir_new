<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReceiveNotifApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Receive notification from vendor API.
     * 
     * The decrypted payload is expected to contain:
     * - syslog: string
     * - tgl_transaksi: datetime
     * - merchant_id: integer
     * - merchant_name: string
     * - jumlah: integer
     * - issuer_name: string
     * - status: string
     * - pesan_notif: string
     * - tgl_notif: datetime
     * - sender_name: string (optional)
     */
    public function receive(Request $request)
    {
        try {
            $payload = $request->input('decrypted_payload');

            // Validate the decrypted data
            $validator = Validator::make($payload, [
                'syslog'         => 'required|string|max:50',
                'tgl_transaksi'  => 'required|date',
                'merchant_id'    => 'required|integer',
                'merchant_name'  => 'required|string|max:50',
                'jumlah'         => 'required|integer',
                'issuer_name'    => 'required|string|max:50',
                'status'         => 'required|string|max:25',
                'pesan_notif'    => 'required|string',
                'tgl_notif'      => 'required|date',
                'sender_name'    => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Save to database
            $notification = ReceiveNotifApi::create($validator->validated());

            Log::info('Notification API: Data received successfully', [
                'id' => $notification->id,
                'merchant_id' => $notification->merchant_id,
                'jumlah' => $notification->jumlah,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification received successfully.',
                'data'    => [
                    'id' => $notification->id,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Notification API: Failed to save data', [
                'ip'    => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }
}
