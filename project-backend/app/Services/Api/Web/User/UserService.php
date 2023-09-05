<?php

namespace App\Services\Api\Web\User;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\VerifyCode;
use App\Models\Reset;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseController
{
    /**
     * 用户数据业务
     * @author Neo
     * @param $request
     * @return mixed
     */
    public static function info($request)
    {
        $user = $request->user();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $user);
    }

    /**
     * 激活业务：发送邮箱/手机号码
     *
     * @param $input
     * @return void
     */
    public static function forget($input)
    {
        $reset = [];
        if (!empty($input['email'])) {
            $emailOrPhone = 'email';
            $reset['type'] = 1;
            $reset['email'] = $input['email'];
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $emailOrPhone = 'phone';
            $reset['type'] = 2;
            $reset['area_code'] = $input['area_code'];
            $reset['phone'] = $input['phone'];
        }
        $user = User::where($emailOrPhone, $input[$emailOrPhone])
            ->orderBy('created_at', 'DESC')
            ->first();

        if (empty($user)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到用户', []);
        }

        $code = VerifyCode::findUserVerificationCode($input, $emailOrPhone);
        if (empty($code)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '验证码错误或失效', []);
        }

        DB::beginTransaction();

        // 重设密码
        $update = $user->update([
            'password' => app('hash')->make(request('password')),
        ]);

        $reset['user_id'] = $user['id'];
        $result = Reset::create($reset);
        if (!$result) {
            DB::rollBack();
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '重设密码失败', []);
        }

        $credentials['username'] = $user['username'];
        $credentials['password'] = request('password');

        if (!Auth::attempt($credentials)) {
            DB::rollBack();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, '账号名和密码不匹配', []);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();

        $data = [
            'status' => true,
            'userId' => $user['id'],
            'username' => $user['username'],
            'description' => $user['description'],
            'token' => $tokenResult->accessToken,
            'tokenType' => 'Bearer',
            'expiresTime' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ];

        DB::commit();

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '重设密码成功', $data);
    }

    /**
     * 检查新用户验证码：是否存在，是否在有效时间
     *
     * @param $input
     * @return void
     */
    public static function checkVerificationCode($input)
    {
        $data = VerifyCode::where('code', $input['code'])
            ->where('created_at', '>=', config("setting.verification_code_expires_time"))
            ->first();
        if (empty($data)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '验证码错误', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '验证码正确', $data);
    }

    /**
     * 业务，用户更新数据
     *
     * @param $input
     * @return void
     */
    public static function userUpdateInfo($input, $user)
    {
        if (empty($input)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '没有传递修改数据', []);
        }

        if (!empty($input['username'])) {
            $username = User::where('username', $input['username'])
                ->where('id', '!=', $user['id'])
                ->first();
            if (!empty($username)) {
                return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名已被使用', []);
            }
        }

        $update = [];
        $update = self::issetFieldArray($update, $input, ['username', 'description']);

        $result = $user->update($update);
        if (!$result) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '更新资料失败', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '更新资料成功', $user);
    }

    /**
     * 业务，用户修改密码
     *
     * @param $input
     * @return void
     */
    public static function userChangePassword($input, $user)
    {
        if (!Hash::check($input['oldPassword'], $user->password)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '输入的密码和旧密码不一致', []);
        }

        if ($input['newPassword'] != $input['confirmNewPassword']) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '两次输入的新密码不相同', []);
        }

        if (Hash::check($input['newPassword'], $user->password)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '输入的新密码和旧密码相同', []);
        }

        // 更新密码
        $result = $user->update([
            'password' => app('hash')->make($input['newPassword']),
        ]);
        if (!$result) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '密码更新失败', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '更新密码成功', $user);
    }
}
