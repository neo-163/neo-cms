<?php

namespace App\Http\Controllers\Api\Web\Payment;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\Validator;

class PayPalController extends BaseController
{
    /**
     * @description: 支付
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
     */
    public function pay(Request $request)
    {
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', []);
    }
}
