<?php

namespace App\Services\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\Models\AdminUser;
use App\PromptCode\SystemCode;
use Illuminate\Http\Request;

class UserService extends BaseController
{
    /**
     * @description: 用户列表
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function list($input)
    {
        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }
        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }
        $data = AdminUser::orderBy('id', $input['sort'])->paginate($input['limit'])->toArray();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 用户数据
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function info($input)
    {
        $data = $input['adminUser'];
        $data['roles'] = ['admin'];
        $data['name'] = $data['username'];
        $data['avatar'] = '/admin/images/user.png';
        // $data['introduction'] = '';
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }
}
