<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SingleAccessMiddleware
{
    /**
     * The wait time before retrying (in seconds).
     */
    protected $waitTime = 5;

    /**
     * The lock duration (in seconds).
     */
    protected $lockTime = 30;

    /**
     * Handle the incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Create a unique lock key for this route
        $lockKey = 'route-lock:' . $request->route()->getName();

        // Attempt to acquire the lock
        $lock = Cache::lock($lockKey, $this->lockTime);

        if ($lock->get()) {
            try {
                // Proceed with the request
                return $next($request);
            } finally {
                // Release the lock after processing
                $lock->release();
            }
        } else {
            // If the lock is already held by another user
            return response()->json([
                'message' => 'This route is currently in use by another user. Please try again later.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
    }
}
