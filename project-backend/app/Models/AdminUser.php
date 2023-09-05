<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\AdminRole;

class AdminUser extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'email_verified_at',
        'remember_token',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @description: 用户和角色的多对多关系
     * @author: Neo
     * @return {*}
     */
    public function userRoles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_res', 'user_id', 'role_id');
    }

    /**
     * @description: 列表+搜索
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $data = self::where('deleted_at', '=', NULL);

        if (!empty($input['title'])) {
            $data = $data->where('title', 'like', '%' . $input['title'] . '%');
        }

        if (!empty($input['status'])) {
            $data = $data->where('status', $input['status']);
        }

        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }

        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }

        // 关联查询，toArray()才能处理关联数组
        return $data->orderBy('id', $input['sort'])->paginate($input['limit'])->toArray();
    }
}
