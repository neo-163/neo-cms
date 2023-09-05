<?php

namespace App\Services\Api\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;

class TestService extends BaseController
{
    /**
     * 测试
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public static function test($input)
    {
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $input);
    }
}
