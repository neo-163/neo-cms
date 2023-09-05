<?php

namespace App\Http\Controllers\Api\Web\Page;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Web\Page\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends BaseController
{
    /**
     * 页面详情
     * @author Neo
     * @param $details
     * @return mixed
     */
    public function details($details)
    {
        $input['url_tag'] = $details;
        return PageService::details($input);
    }
}
