<?php

namespace App\Services\Api\Admin\AdminRolePermission;

use App\Http\Controllers\BaseController;
use App\Models\AdminRole;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\DB;

class AdminRoleService extends BaseController
{
    /**
     * @description: 列表，ASC：最早，DESC：最新
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $data = AdminRole::list($input);
        $data['data'] = self::sortData($data['data']);

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
            $array['description'] = $data['description'];
            $array['parent'] = $data['parent'];
            $array['sort'] = $data['sort'];
            $array['status'] = $data['status'];

            if ($data['parent'] == 0) {
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
        $data = AdminRole::find($input['id']);
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
        $sorts = AdminRole::where('parent', $input['parent'] ?? 0)->where('sort', $input['sort']);

        if (!empty($input['id'])) {
            $sorts = $sorts->where('id', '!=', $input['id'])->first();
            $data = AdminRole::find($input['id']);
            if (empty($data)) {
                return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
            }
        } else {
            $sorts = $sorts->first();
            $data = new AdminRole();
        }

        if (!empty($sorts)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '同级排序数字sort不可重复', []);
        }

        $data->title = $input['title'];
        $data->description = $input['description'] ?? '';
        $data->parent = $input['parent'] ?? SystemCode::CODE_NO;
        $data->sort = $input['sort'];
        $data->status = $input['status'] ?? SystemCode::CODE_YES;

        DB::beginTransaction();

        $create = $data->save();
        if (!$create) {
            DB::rollBack();
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '操作失败', []);
        }

        // 处理角色的权限集合
        if (!empty($input['role_permissions'])) {
            // 新增多出元素，和删除减少元素
            $data->rolePermissions()->sync($input['role_permissions']);
        }

        DB::commit();
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
        $delete = AdminRole::whereIn('id', $input['id'])->delete();
        $data['delete_num'] = $delete;
        if ($delete == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }
}
