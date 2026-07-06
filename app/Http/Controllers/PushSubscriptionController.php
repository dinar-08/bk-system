<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan / perbarui subscription push browser milik user yang sedang login.
     * Dipanggil dari JS setelah user mengizinkan notifikasi (Notification.requestPermission
     * -> pushManager.subscribe) di layout.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $request->user()->updatePushSubscription(
            endpoint: $validated['endpoint'],
            key: $validated['keys']['p256dh'],
            token: $validated['keys']['auth'],
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Hapus subscription (mis. saat user menonaktifkan notifikasi di device tersebut).
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['status' => 'ok']);
    }
}