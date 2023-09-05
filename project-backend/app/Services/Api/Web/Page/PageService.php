<?php

namespace App\Services\Api\Web\Page;

use App\Http\Controllers\BaseController;
use App\Models\Page;
use App\PromptCode\SystemCode;

class PageService extends BaseController
{
    /**
     * 页面详情
     * @author Neo
     * @param $input
     * @return mixed
     */
    public static function details($input)
    {
        $data = Page::where('id', $input['id'])->first();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
