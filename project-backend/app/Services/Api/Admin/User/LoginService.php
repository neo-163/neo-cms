<?php

namespace App\Services\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\Models\AdminUser;
use App\Models\AdminUserToken;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\Hash;

class LoginService extends BaseController
{
    /**
     * @description: 注册
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function register($input)
    {
        $usernameStr = strstr($input['username'], '@');
        if ($usernameStr) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名不可有@字符', []);
        }

        $username = AdminUser::where('username', $input['username'])->first();
        if (!empty($username)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名已经存在', []);
        }

        $email = AdminUser::where('email', $input['email'])->first();
        if (!empty($email)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '邮箱已经存在', []);
        }

        // 创建新用户
        $user = new AdminUser();
        $user->username = $input['username'];
        $user->email = $input['email'];
        $user->password = app('hash')->make($input['password']);

        if (!$user->save()) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '注册失败', $input);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '注册成功', $input);
    }

    /**
     * @description: 登录
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function login($input)
    {
        $user = AdminUser::where('username', $input['username'])->first();
        if (empty($user)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到用户', []);
        }

        if (!Hash::check($input['password'], $user->password)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '密码错误', []);
        }

        // 后台token加密算法 start
        $token = bcrypt(time()) . env('TOKENKEY', 'blank') . bcrypt(time());
        $token = base64_encode($token);
        // 后台token加密算法 end

        // token失效时间
        $expiresTime = date("Y-m-d H:i:s", strtotime("+1 day"));
        if (!empty($input['remember_me']) && $input['remember_me']) {
            $expiresTime = date("Y-m-d H:i:s", strtotime("+30 day"));
        }

        // 新增token和有效时间
        $data = [
            'user_id' => $user['id'],
            'token' => $token,
            'active_time' => date("Y-m-d H:i:s"),
            'expires_time' => $expiresTime,
        ];
        $createToken = AdminUserToken::create($data);
        if (!$createToken) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '创建token失败', []);
        }

        $data['username'] = $user['username'];
        $data['token_type'] = 'Bearer';

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '登录成功', $data);
    }

    /**
     * @description: 登出
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function logout($input)
    {
        // 删除指定token和失效的token
        // $where = [
        //     ['user_id', '=', $request['adminUser']['id'] ],
        //     ['expires_time', '<', date("Y-m-d H:i:s")],
        // ];
        // $delete = AdminUserToken::where(['user_id', $request['adminUser']['id'] ])->detele();
        // $data[] = $delete;

        // return '开发中';

        $data = [];
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '登出成功', $data);
    }
}
