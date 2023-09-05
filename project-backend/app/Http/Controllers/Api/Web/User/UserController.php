<?php

namespace App\Http\Controllers\Api\Web\User;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\PromptCode\SystemCode;
use App\Services\Api\Web\User\UserService;
use App\Services\Api\Web\User\SendService;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    /**
     * @description: 用户数据接口
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function info(Request $request)
    {
        return UserService::info($request);
    }

    /**
     * @description: 发送验证码接口，注册时
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function sendVerificationCodeByRegister(Request $request)
    {
        $input = $request->only('email', 'area_code', 'phone');
        $validationCondition = [
            'email' => 'nullable|string|email',
            'area_code' => 'nullable|integer',
            'phone' => 'nullable|integer',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return SendService::sendVerificationCodeByRegister($input);
    }

    /**
     * @description: 发送验证码接口，找回密码时
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function sendVerificationCodeByForget(Request $request)
    {
        $input = $request->only('email', 'area_code', 'phone');
        $validationCondition = [
            'email' => 'nullable|string|email',
            'area_code' => 'nullable|integer',
            'phone' => 'nullable|integer',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return SendService::sendVerificationCodeByForget($input);
    }

    /**
     * @description: 检查验证码接口
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function checkVerificationCode(Request $request)
    {
        $input = $request->only('code');
        $validationCondition = [
            'code' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return UserService::checkVerificationCode($input);
    }

    /**
     * @description: 找回密码接口
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function forget(Request $request)
    {
        // 验证参数
        $input = $request->only('email', 'area_code', 'phone', 'code', 'password', 'password_confirmation');
        $validationCondition = [
            'description' => 'nullable|string',
            'email' => 'nullable|string|email',
            'area_code' => 'nullable|integer',
            'phone' => 'nullable|integer',
            'code' => 'required|string',
            'password' => 'required|string|confirmed|min:6|max:100',
            'password_confirmation' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return UserService::forget($input);
    }

    /**
     * @description: 更新用户资料，后续更新邮箱，电话
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function userUpdateInfo(Request $request)
    {
        // 验证参数
        $input = $request->only('username', 'description');
        $validationCondition = [
            'username' => 'nullable|string',
            'description' => 'nullable|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        $user = $request->user();
        return UserService::userUpdateInfo($input, $user);
    }

    /**
     * @description: 用户修改密码
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function userChangePassword(Request $request)
    {
        // 验证参数
        $input = $request->only('oldPassword', 'newPassword', 'confirmNewPassword');
        $validationCondition = [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string',
            'confirmNewPassword' => 'required|string',
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        $user = $request->user();
        return UserService::userChangePassword($input, $user);
    }

    /**
     * 测试-本地发送邮箱
     */
    public static function testsendEmailLocal(Request $request)
    {
        $data = [
            'content' => request('content'),
            'subject' => request('subject'),
            'email' => request('email'),
        ];
        $mail = Mail::raw($data['content'], function ($message) use ($data) {
            $message->subject($data['subject']);
            $message->to($data['email']);
        });

        Log::info('Queue - Send email time: '.date('Y-m-d H:i:s'));
        return true;
    }

    /**
     * 测试-队列发送邮箱
     */
    public static function testsendEmailQueue(Request $request)
    {
        $data = [
            'content' => request('content'),
            'subject' => request('subject'),
            'email' => request('email'),
        ];
        $mail = Mail::raw($data['content'], function ($message) use ($data) {
            $message->subject($data['subject']);
            $message->to($data['email']);
        });

        Log::info('Queue - Send email time: '.date('Y-m-d H:i:s'));
        return true;
    }
}
