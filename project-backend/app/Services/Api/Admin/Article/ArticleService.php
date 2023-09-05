<?php

namespace App\Services\Api\Admin\Article;

use App\Http\Controllers\BaseController;
use App\Models\Article;
use App\PromptCode\SystemCode;

class ArticleService extends BaseController
{
    /**
     * 页面列表，ASC：最早，DESC：最新
     * @author Neo
     * @param $input
     * @return mixed
     */
    static function list($input)
    {
        $input['showArray'] = ['id', 'username'];
        $data = Article::list($input);
        foreach ($data['data'] as &$articleList) {
            $articleList['article_author'] = self::handleNullArray($articleList['article_author'], $input['showArray']);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 详情
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    static function details($input)
    {
        $data = Article::where('id', $input['id'])->first();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 创建
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    static function create($input)
    {
        if (!empty($input['json'])) {
            $input['json'] = json_decode($input['json'], true);
        }

        $data = [
            'title' => $input['title'],
            'image' => $input['image'] ?? '',
            'content' => $input['content'] ?? '',
            'json' => $input['json'] ?? [],
            'status' => $input['status'] ?? SystemCode::CODE_ARTICLE_DRAFT,
            'remarks' => $input['remarks'] ?? '',
        ];
        $create = Article::create($data);
        if (!$create) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '创建失败', $create);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '创建成功', $create);
    }

    /**
     * @description: 编辑
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    static function edit($input)
    {
        if (!empty($input['json'])) {
            $input['json'] = json_decode($input['json'], true);
        }

        $data = [
            'title' => $input['title'],
            'image' => $input['image'] ?? '',
            'content' => $input['content'] ?? '',
            'json' => $input['json'] ?? [],
            'status' => $input['status'] ?? SystemCode::CODE_ARTICLE_PUBLISH,
            'remarks' => $input['remarks'] ?? '',
        ];
        $update = Article::where('id', $input['id'])->update($data);
        if (!$update) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '编辑失败', $data);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '编辑成功', $data);
    }

    /**
     * @description: 删除
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function delete($input)
    {
        $delete = Article::whereIn('id', $input['id'])->delete();
        $data['delete_num'] = $delete;
        if ($delete == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }
}
