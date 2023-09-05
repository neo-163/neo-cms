<?php

namespace App\Http\Controllers\Blade\Web;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
// use App\Services\Web\IndexService;

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
        // return view('web.test.phpinfo', compact('data'));
        return view('web.test.index', compact('data'));
    }

    /**
     * 测试
     * @author Neo
     * @return mixed
     */
    public function test()
    {
        $data['text'] = 'test1';
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
