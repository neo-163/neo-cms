<?php

namespace App\Http\Controllers\Api\Web\Article;

use App\Http\Controllers\BaseController;
use App\PromptCode\SystemCode;
use App\Services\Api\Web\Article\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends BaseController
{
    /**
     * @description: 文章列表
     * @Creator: Neo
     * @param {Request} $request
     * @return {*}
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

        return ArticleService::list($input);
    }

    /**
     * @description: 文章详情
     * @Creator: Neo
     * @param {*} $details
     * @return {*}
     */
    public function details($details)
    {
        $input['url_tag'] = $details;
        return ArticleService::details($input);
    }

    /**
     * @description: 最新文章
     * @author: Neo
     * @return {*}
     */     
    public function newList()
    {
        return ArticleService::newList();
    }
}
