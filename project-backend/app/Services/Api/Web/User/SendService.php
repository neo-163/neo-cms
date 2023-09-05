<?php

namespace App\Services\Api\Web\User;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmail;
use App\Models\VerifyCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\Log;

class SendService extends BaseController
{
    /**
     * @description: 注册发送验证码业务，ASC：最早，DESC：最新
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function sendVerificationCodeByRegister($input)
    {
        if (!empty($input['email'])) {
            $content = 'email';
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $content = 'phone';
        } else {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '没有有效邮箱或电话号码', []);
        }

        $user = User::where($content, $input[$content])
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!empty($user)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '邮箱已经注册，请登录', []);
        }

        return self::sendVerificationCode($input);
    }

    /**
     * 注册发送验证码业务，ASC：最早，DESC：最新
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public static function sendVerificationCodeByForget($input)
    {
        if (empty($input)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '没有传递修改数据', []);
        }

        if (!empty($input['email'])) {
            $content = 'email';
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $content = 'phone';
        } else {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '没有有效邮箱或电话号码', []);
        }

        $user = User::where($content, $input[$content])
            ->orderBy('created_at', 'DESC')
            ->first();

        if (empty($user)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到有效邮箱或电话号码，请注册', []);
        }

        return self::sendVerificationCode($input);
    }

    /**
     * 发送验证码业务
     * 激活逻辑：检查邀请码30秒才能发送
     *
     * @param Request $request
     * @return void
     */
    public static function sendVerificationCode($input)
    {
        if (!empty($input['email'])) {
            $content = 'email';
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $content = 'phone';
        }

        $send = VerifyCode::where($content, $input[$content])
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!empty($send)) {
            $data['time_interval'] = time() - strtotime($send['created_at']);
            $time_interval = config("setting.verification_code_sending_interval");
            $output_time_interval = config("setting.output_verification_code_sending_interval");
            if ($data['time_interval'] <= $time_interval) {
                return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '超过' . $output_time_interval . '秒后，才能发送第二次验证码', $data);
            }
        }

        $data = [];
        if (!empty($input['email'])) {
            $data['type'] = 1;
            $data['email'] = $input['email'];
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            $data['type'] = 2;
            $data['area_code'] = $input['area_code'];
            $data['phone'] = $input['phone'];
        }

        $data['user_id'] = 0;

        $code = mt_rand(100000, 999999);
        $data['code'] = $code;

        DB::beginTransaction();

        if (!empty($input['email'])) {

            $data['content'] = '您在' . env('MAIL_FROM_NAME') . '操作的验证码是： ';
            $data['content'] .= $code;
            $data['content'] .= ' <p>此邮件是系统自动发送，无需回复</p>';

            $data['subject'] = env('MAIL_FROM_NAME') . '验证码，无需回复';
            $data['email'] = request('email');

            // 队列发送邮箱
            // SendEmail::dispatch($data);
            SendEmail::dispatch($data)->onConnection(env('QUEUE_DRIVER'))->onQueue('send_email');
        } elseif (!empty($input['area_code']) && !empty($input['phone'])) {
            // 短信发送给手机号
        }

        // 生成验证码数据
        $result = VerifyCode::create($data);
        if (!$result) {
            DB::rollBack();
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '生成验证码数据失败', []);
        }

        DB::commit();

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '验证码发送成功，请查看邮箱，有效时间' . config("setting.output_verification_code_expires_time") . '分钟', $data);
    }

    /**
     * 本地发送邮箱
     */
    public static function sendEmailTest($data)
    {
        // 本地发送邮箱
        // SendEmail::dispatch($data);
        // dispatch(new SendEmail($data));

        // 队列发送邮箱
        // $send = SendEmail::dispatch($data)->onConnection('rabbitmq')->onQueue('send_email');

        $mail = Mail::raw($data['content'], function ($message) use ($data) {
            $message->subject($data['subject']);
            $message->to($data['email']);
        });

        // Mail::send
        // 可以保存到 TXT
        //  等于或大于1，等于有失败情况
        // if (count(Mail::failures()) > 0) {
        //     foreach (Mail::failures() as $email_address) {
        //         echo $email_address . " | ";
        //     }
        // }

        Log::info('Local - Send email time: ' . date('Y-m-d H:i:s'));
        return true;
    }
}
