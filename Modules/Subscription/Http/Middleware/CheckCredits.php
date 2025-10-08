<?php

namespace Modules\Subscription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCredits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $requiredCredits)
    {
        /** @var \App\Models\User|\App\Models\DeliveryMan|\App\Models\Store $user */
        $user = Auth::user();

        if (!$user || $user->creditBalance() < $requiredCredits) {
            return response()->json(['message' => 'Insufficient credits.'], 403);
        }

        return $next($request);
    }
}
