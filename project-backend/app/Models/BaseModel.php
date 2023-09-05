<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Models\VerifyCode;

class BaseModel extends Model
{
    use HasFactory;

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        // return $date->format('Y-m-d H:i:s');
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * @description: 寻找指定用户的验证码：验证码是否存在，是否指定用户的邮箱/手机号码申请，是否在有效时间
     * @author: Neo
     * @param {*} $input
     * @param {*} $emailOrPhone
     * @return {*}
     */     
    public static function findUserVerificationCode($input, $emailOrPhone)
    {
        $code = VerifyCode::where('code', $input['code'])
            ->where($emailOrPhone, $input[$emailOrPhone])
            ->where('created_at', '>=', config("setting.verification_code_expires_time"))
            ->first();

        return $code;
    }
}
