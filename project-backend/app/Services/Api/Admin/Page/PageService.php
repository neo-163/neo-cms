<?php

namespace App\Services\Api\Admin\Page;

use App\Http\Controllers\BaseController;
use App\Models\Page;
use App\PromptCode\SystemCode;

class PageService extends BaseController
{
    /**
     * 页面列表，ASC：最早，DESC：最新
     * @author Neo
     * @param $input
     * @return mixed
     */
    public static function list($input)
    {
        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }
        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }

        $input['showArray'] = ['id', 'username'];
        $data = Page::list($input);
        foreach ($data['data'] as &$articleList) {
            $articleList['page_author'] = self::handleNullArray($articleList['page_author'], $input['showArray']);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 详情
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    public static function details($input)
    {
        $data = Page::find($input['id']);
        if (empty($data)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 创建
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    public static function create($input)
    {
        $item = Page::where('url_tag', $input['url_tag'])->first();
        if ($item) {
            $tag['url_tag'] = $item['url_tag'];
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '页面链接标签已经存在', $tag);
        }

        if (!empty($input['json'])) {
            $input['json'] = json_decode($input['json'], true);
        }

        $data = [
            'title' => $input['title'],
            'image' => $input['image'] ?? '',
            'content' => $input['content'] ?? '',
            'url_tag' => $input['url_tag'],
            'json' => $input['json'] ?? [],
            'status' => $input['status'] ?? SystemCode::CODE_ARTICLE_DRAFT,
            'remarks' => $input['remarks'] ?? '',
        ];
        $create = Page::create($data);
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
    public static function edit($input)
    {
        if (empty($input['url_tag'])) {
            $tag['url_tag'] = $input['url_tag'];
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '链接标签不能为空', $tag);
        }

        $item = Page::where('url_tag', $input['url_tag'])->first();
        if ($item && $item['id'] != $input['id']) {
            $tag['url_tag'] = $item['url_tag'];
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '链接标签已经存在', $tag);
        }

        $input['json'] = json_decode($input['json'], true);

        $data = [
            'title' => $input['title'],
            'image' => $input['image'] ?? '',
            'content' => $input['content'] ?? '',
            'url_tag' => $input['url_tag'],
            'json' => $input['json'] ?? [],
            'status' => $input['status'] ?? SystemCode::CODE_ARTICLE_PUBLISH,
            'remarks' => $input['remarks'] ?? '',
        ];
        $update = Page::where('id', $input['id'])->update($data);
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
    public static function delete($input)
    {
        $delete = Page::whereIn('id', $input['id'])->delete();
        $data['delete_num'] = $delete;
        if ($delete == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }
}
