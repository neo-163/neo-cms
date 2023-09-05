<?php

namespace App\Services\Api\Admin\Media;

use App\Http\Controllers\BaseController;
use App\Models\Media;
use App\Models\MediaCategory;
use App\PromptCode\SystemCode;

class MediaCategoryService extends BaseController
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

        $showArray = ['id', 'username'];
        $data = MediaCategory::with(['mediaCategoryCreator' => function ($query) use ($showArray) {
            $query->select($showArray);
        }]);

        if (!empty($input['title'])) {
            $data = $data->where('title', 'like', '%' . $input['title'] . '%');
        }
        if (!empty($input['status'])) {
            $data = $data->where('status', $input['status']);
        }
        $data = $data->orderBy('id', $input['sort'])->paginate($input['limit'])->toArray();

        // 关联查询，上面toArray()才能处理关联数组
        foreach ($data['data'] as &$articleList) {
            $articleList['media_category_creator'] = self::handleNullArray($articleList['media_category_creator'], $showArray);
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
        $data = MediaCategory::find($input['id']);
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
        $data = [
            'title' => $input['title'],
            'description' => $input['description'] ?? '',
            "sort" => $input['sort'] ?? 0,
            "user_id" => $input['adminUser']['id'],
        ];
        $create = MediaCategory::create($data);
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
        $data = [
            'title' => $input['title'],
            'description' => $input['description'] ?? '',
            "user_id" => $input['adminUser']['id'],
        ];
        if (!empty($input['sort'])) {
            $data['sort'] =  $input['sort'];
        }
        $update = MediaCategory::where('id', $input['id'])->update($data);
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
        $media = Media::whereIn('category_id', $input['id'])->first();
        if ($media) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '当前分类还有所属媒体', $media);
        }
        $delete = MediaCategory::whereIn('id', $input['id'])->delete();
        $data['delete_num'] = $delete;
        if ($delete == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到分类', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }
}
