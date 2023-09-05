<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Admin\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * 用户数据
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function list(Request $request)
    {
        // 验证参数
        $input = $request->only('page', 'limit');
        $validationCondition = [
            'page' => 'nullable|integer', // 分页号码
            'limit' => 'nullable|integer', // 每页多少记录
        ];
        $validator = Validator::make($input, $validationCondition);
        if ($validator->fails()) {
            $error['message'] = $validator->errors()->first();
            return self::output(SystemCode::CODE_PARAMETER_ERROR, SystemCode::STATUS_ERROR, $error['message'], []);
        }

        return UserService::list($input);
    }
    
    /**
     * 用户数据
     * @author Neo
     * @param Request $request 请求
     * @return mixed
     */
    public function info(Request $request)
    {
        return UserService::info($request);
    }
}
