<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class AdminPermission extends BaseModel
{
    use HasFactory;

    /**
     * @description: 模型的查询处理：列表+搜索
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $data = new self;

        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }

        $whereArray = ['title', 'description'];
        foreach ($whereArray as $item) {
            if (!empty($input[$item])) {
                $data = $data->where($item, 'like', '%' . $input[$item] . '%');
            }
        }

        if (!empty($input['status'])) {
            $data = $data->where('status', $input['status']);
        }

        // 如有关联表的查询，toArray()才能处理关联数组
        return $data->orderBy('sort', $input['sort'])->get()->toArray();
    }

    /**
     * @description: 模型的查询处理：规则权限的分级选项列表
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function selects($input)
    {
        $data = self::where('id', '!=', $input['id']);

        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }

        if (!empty($input['status'])) {
            $data = $data->where('status', $input['status']);
        }

        // 如有关联表的查询，toArray()才能处理关联数组
        return $data->orderBy('sort', $input['sort'])->get()->toArray();
    }
}
