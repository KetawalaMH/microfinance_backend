<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Setting;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $build_number = intval($request->header('Build-Number'));
            $maintain_data = Setting::select('value')->where('id', 1)->where('is_active', 1)->orderBy('id', 'DESC')->first();
            $maintain_mode = isset($maintain_data->value) ? intval($maintain_data->value) : 0;
            if($maintain_mode == 1) {
                $output = [
                    'success' => false,
                    'message' => "Server in maintain mode.",
                    'data' => ['maintain_mode' => 1]
                ];

                return response()->json($output, 503);
            } else {
                if ($build_number > 0) {
                    $version_data = Version::where('build_number', '>', $build_number)
                                            ->where('is_Active', 1)
                                            ->orderBy('id', 'DESC')->first();

                    if (isset($version_data->id)) {
                        $output = [
                            'success' => false,
                            'message' => "You have a critical app update.",
                            'data' => ['has_update' => 1]
                        ];

                        return response()->json($output, 403);
                    }
                } else {
                    $user = JWTAuth::parseToken()->authenticate();
                    if(isset($user->id)) {
                        $user_id1 = isset($user->id) ? intval($user->id) : 0;
                        $user_id2 = intval($request->header('USER_ID'));
                        if($user_id1 != $user_id2) {
                            $output['success'] = false;
                            $output['data'] = null;
                            $output['message'] = "User not match";
                            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 403);
                        }
                    } else {
                        $output['success'] = false;
                        $output['data'] = null;
                        $output['message'] = "User not exit";
                        return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 401);
                    }
                    
                }
            }
            
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Token is Invalid";
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Token is Expired";
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 401);
            } else {
                $output['success'] = false;
                $output['data'] = null;
                $output['message'] = "Authorization Token not found";
                return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 401);
            }
        }
        return $next($request);
    }
}
