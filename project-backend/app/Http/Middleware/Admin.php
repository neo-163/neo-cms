<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AdminUser;
use App\Models\AdminUserToken;
use App\Http\Controllers\BaseController;

class Admin extends BaseController
{
    /**
     * Handle an incoming request.
     * @author Neo
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $passToken = AdminUserToken::where('token', $token)->first();

        if (empty($passToken)) {
            return self::output(40002, 'success', '未登录', []);
        }

        if (date("Y-m-d H:i:s") > $passToken['expires_time']) {
            return self::output(200, 'success', 'token已失效', []);
        }

        $request['adminUser'] = AdminUser::where('id', $passToken['user_id'])->first();

        return $next($request);
    }
}
