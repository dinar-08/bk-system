<?php
namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ]);

        $subscriptionModel = config('webpush.model') ?? \NotificationChannels\WebPush\PushSubscription::class;

        $subscriptionModel::where('endpoint', $validated['endpoint'])
            ->where(function ($query) use ($request) {
                $query->where('subscribable_id', '!=', $request->user()->id)
                    ->orWhere('subscribable_type', '!=', get_class($request->user()));
            })
            ->delete();

        $request->user()->updatePushSubscription(
            endpoint: $validated['endpoint'],
            key: $validated['keys']['p256dh'],
            token: $validated['keys']['auth'],
            contentEncoding: 'aes128gcm',
        );

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);
        $request->user()->deletePushSubscription($validated['endpoint']);
        return response()->json(['status' => 'ok']);
    }
}