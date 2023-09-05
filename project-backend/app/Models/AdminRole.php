<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\AdminRolePermission;
use App\Models\AdminRoleRe;

class AdminRole extends BaseModel
{
    use HasFactory;

    /**
     * @description: 角色和权限的多对多关系
     * @author: Neo
     * @return {*}
     */
    public function rolePermissions()
    {
        return $this->belongsToMany(AdminRolePermission::class, 'admin_role_permission_res', 'role_id', 'permission_id');
    }

    /**
     * @description: 角色有多少个用户
     * @author: Neo
     * @return {*}
     */
    public function userNumber()
    {
        return $this->hasMany(AdminRoleRe::class, 'role_id', 'id');
    }

    /**
     * @description: 模型的查询处理：列表+搜索
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $data = self::withCount('userNumber');

        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }

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
        return $data->orderBy('sort', $input['sort'])->paginate($input['limit'])->toArray();
    }
}
