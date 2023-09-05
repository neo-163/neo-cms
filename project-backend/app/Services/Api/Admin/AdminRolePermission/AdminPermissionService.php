<?php

namespace App\Services\Api\Admin\AdminRolePermission;

use App\Http\Controllers\BaseController;
use App\Models\AdminPermission;
use App\PromptCode\SystemCode;

class AdminPermissionService extends BaseController
{
    /**
     * @description: 列表，ASC：最早，DESC：最新
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $data = AdminPermission::list($input);
        $data = self::sortData($data);

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
    
    /**
     * @description: 规则权限的分级选项列表
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function selects($input)
    {
        $data = AdminPermission::selects($input);
        $data = self::sortData($data);

        $first = [
            'id' => 0,
            "title" => '根目录',
            "label" => '根目录',
            "route" => '/',
        ];
        array_unshift($data, $first);

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 处理无限分级的数据
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function sortData($input)
    {
        $ref = [];
        $result = [];
        foreach ($input as $data) {
            $array = &$ref[$data['id']];
            $array['id'] = $data['id'];
            $array['title'] = $data['title'];
            $array['label'] = $data['title'];
            $array['route'] = $data['route'];
            $array['description'] = $data['description'];
            $array['parent'] = $data['parent'];
            $array['sort'] = $data['sort'];
            $array['status'] = $data['status'];

            if ($data['parent'] == 0) {
                // $result[$data['sort']] = &$array;
                $key = $key ?? 0;
                $result[$key] = &$array;
                $key++;
            } else {
                $key = $ref[$data['parent']]['children_count'] ?? 0;
                $ref[$data['parent']]['children_count'] = $key + 1;
                $ref[$data['parent']]['children'][$key] = &$array;
            }
        }

        return $result;
    }

    /**
     * @description: 详情
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function details($input)
    {
        $data = AdminPermission::find($input['id']);
        if (empty($data)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 保存：新增+编辑
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function store($input)
    {
        // 同级排序数字sort不可重复，否则会影响sortData()的展示结果
        $sorts = AdminPermission::where('parent', $input['parent'] ?? 0)->where('sort', $input['sort']);

        if (!empty($input['id'])) {
            $sorts = $sorts->where('id', '!=', $input['id'])->first();
            $data = AdminPermission::where('id', $input['id'])->first();
            if (empty($data)) {
                return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
            }
        } else {
            $sorts = $sorts->first();
            $data = new AdminPermission();
        }

        if (!empty($sorts)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '同级排序数字sort不可重复', []);
        }

        $data->title = $input['title'];
        $data->route = $input['route'];
        $data->description = $input['description'] ?? '';
        $data->parent = $input['parent'] ?? SystemCode::CODE_NO;
        $data->sort = $input['sort'];
        $data->status = $input['status'] ?? SystemCode::CODE_YES;

        $create = $data->save();
        if (!$create) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '操作失败', []);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '操作成功', []);
    }

    /**
     * @description: 删除
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function delete($input)
    {
        $delete = AdminPermission::whereIn('id', $input['id'])->delete();
        $data['delete_num'] = $delete;
        if ($delete == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }
}
