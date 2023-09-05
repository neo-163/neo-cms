<?php

namespace App\Services\Api\Web\Article;

use App\Http\Controllers\BaseController;
use App\Models\Article;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\Redis;

class ArticleService extends BaseController
{
    protected static $newList = 'web:article:newList'; // 最新文章列表

    /**
     * @description: 文章列表
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function list($input)
    {
        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }

        $data = Article::paginate($input['limit'])->toArray();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 文章详情
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function details($input)
    {
        $data = Article::where('url_tag', $input['url_tag'])->first();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 最新文章列表
     * 热数据：Redis 的性能大约是普通关系型数据库的 10 - 100 倍
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function newList()
    {
        $data = Redis::get(self::$newList);
        if (!empty($data)) {
            $data = json_decode($data);
        } else {
            $data = Article::get()->take(10)->toArray();
            Redis::set(self::$newList, json_encode($data));
            Redis::expire(self::$newList, 60); // 单位：秒
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
