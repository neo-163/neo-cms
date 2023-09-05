<?php

namespace App\Services\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Article;
use App\PromptCode\SystemCode;

class TestService extends BaseController
{
    /**
     * 查询文章，和依据封面图id关联封面图数据
     * @author Neo
     * @return mixed
     */
    public static function articleList()
    {
        $mediaShowArray = ['id', 'url'];
        $data['articleList'] = Article::with(['articleImage' => function ($query) use ($mediaShowArray) {
            $query->select($mediaShowArray);
        }])->get()->toArray();

        // 关联查询，上面toArray()才能处理关联数组
        foreach ($data['articleList'] as &$articleList) {
            $articleList['article_image'] = self::handleNullArray($articleList['article_image'], $mediaShowArray);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
