<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\GoogleCalendarInterface;
use Symfony\Component\HttpFoundation\Response;

class HasValidAccessToken
{

    protected $gCalService;
    protected $apiClient;

    public function __construct(GoogleCalendarInterface $gCalService)
    {
        $this->gCalService = $gCalService;
        $this->apiClient = $gCalService->getClient();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment() === 'testing') {
            return $next($request);
        }

        if (!$request->user()->access_token) {
            $auth_url = $this->apiClient->createAuthUrl();

            return response()->json([
                'status' => '403',
                'message' => 'user is unauthenticated',
                'data' => $auth_url,
            ], 403);
        }

        if ($request->user()->isTokenExpired()) {
            if ($request->user()->access_token['error']) {
                $request->user()->update(['access_token' => []]);
                return $next($request);
            }

            $token = $this->apiClient
                ->fetchAccessTokenWithRefreshToken($request->user()->access_token['refresh_token']);

            $request->user()->update(['access_token' => $token]);

            return $next($request);
        }

        return $next($request);
    }
}
