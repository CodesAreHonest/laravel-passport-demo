<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class Authorization
{
    private $baseService;
//    public function __construct(BaseService $baseService) {
//        $this->baseService = $baseService;
//    }
    /**
     * Handle an incoming request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $header = $request->header('Authorization');

        if (!$request->headers->has('Authorization')) {
            return response()->json(array(
                'response_code' => 401,
                'response_msg'  => 'Unauthenticated. '
            ), 401);
        }

        $key = file_get_contents(storage_path('oauth-public.key'));

        try {
            JWT::decode(substr($header, 7), $key, array('RS256'));
            return $next($request);

        }
        catch (ExpiredException $e) {
            return response()->json([
                'response_code' => 401,
                'response_msg'  => $e->getMessage()
            ], 401);
        }

    }
}
