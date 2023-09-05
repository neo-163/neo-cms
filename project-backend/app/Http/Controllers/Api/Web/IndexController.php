<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\PromptCode\SystemCode;
// use App\Services\Web\IndexService;
use App\Module\Base;

class IndexController extends BaseController
{
    /**
     * 首页
     * @author Neo
     * @return mixed
     */
    public function index()
    {
        $data['test'] = 1;
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
