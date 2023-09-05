<?php

namespace App\Services\Api\Web\User;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\PromptCode\SystemCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\VerifyCode;

class LoginService extends BaseController
{
    /**
     * @description: 注册逻辑：名字不能有@，和邮箱避免重复
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function register($input)
    {
        $usernameStr = strstr(request('username'), '@');
        if ($usernameStr) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名不可有@字符', []);
        }

        $username = User::where('username', $input['username'])->first();
        if (!empty($username)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名已经存在', []);
        }

        if (!empty($input['email'])) {
            $emailOrPhone = 'email';
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $emailOrPhone = 'phone';
        }
        $item = User::where($emailOrPhone, $input[$emailOrPhone])->first();
        if (!empty($item)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '邮箱或电话号码已经存在，请登录', []);
        }

        $code = VerifyCode::findUserVerificationCode($input, $emailOrPhone);
        if (empty($code)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '验证码错误或失效', []);
        }

        DB::beginTransaction();

        $time = date("Y-m-d H:i:s");

        $user['username'] = $input['username'];
        $user['email'] = $input['email'];
        $user['email_verified_at'] = $time;
        $user['password'] = app('hash')->make(request('password'));
        $user['created_at'] = $time;
        $user['updated_at'] = $time;

        $user = self::issetFieldArray($user, $input, ['description']);

        $create = User::create($user);
        if (!$create) {
            DB::rollBack();
            $data['message'] = $create;
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '创建失败', $data);
        }

        DB::commit();
        return self::login($input, $user);
    }

    /**
     * @description: 登录，加入多次失败的验证
     * @author: Neo
     * @param {*} $input
     * @param {*} $user
     * @return {*}
     */
    public static function login($input, $user)
    {
        $user = User::where('username', request('username'))->first();

        if (empty($user)) {
            $user = User::where('email', request('username'))->first();
            if (empty($user)) {
                return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名和邮箱还没有注册', []);
            }
        }

        $credentials['username'] = $user['username'];
        $credentials['password'] = request('password');

        if (!Auth::attempt($credentials)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '账号名和密码不匹配', []);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if (!empty($input['remember_me'])) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        $data = [
            'status' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'description' => $user['description'],
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_time' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ];
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '登录成功', $data);
    }

    /**
     * @description: 登出
     * @author: Neo
     * @param {Request} $request
     * @return {*}
     */
    public static function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->each(function ($token) {
                $token->delete();
            });

            return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '登出成功', []);
        } catch (\Exception $e) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, $e->getMessage(), []);
        }
    }

    /**
     * @description: 生成邀请码，废弃
     * @author: Neo
     * @return {*}
     */
    public static function createInvitecode()
    {
        // 生成字母和数字组成的6位字符串
        $str = range('A', 'Z');

        // 去除大写的O，以防止与0混淆 
        unset($str[array_search('O', $str)]);
        $arr = array_merge(range(0, 9), $str);
        shuffle($arr);

        $invitecode = '';
        $arr_len = count($arr);
        for ($i = 0; $i < 8; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $invitecode .= $arr[$rand];
        }

        return $invitecode;
    }
}
