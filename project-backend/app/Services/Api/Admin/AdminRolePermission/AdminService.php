<?php

namespace App\Services\Api\Admin\AdminRolePermission;

use App\Http\Controllers\BaseController;
use App\Models\AdminUser;
use App\PromptCode\SystemCode;
use Illuminate\Support\Facades\DB;

class AdminService extends BaseController
{
    /**
     * 页面列表，ASC：最早，DESC：最新
     * @author Neo
     * @param $input
     * @return mixed
     */
    static function list($input)
    {
        $data = AdminUser::list($input);

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
        $data = AdminUser::find($input['id']);
        if (empty($data)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 注册
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function register($input)
    {
        $usernameStr = strstr($input['username'], '@');
        if ($usernameStr) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名不可有@字符', []);
        }

        $username = AdminUser::where('username', $input['username'])->first();
        if (!empty($username)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '用户名已经存在', []);
        }

        $email = AdminUser::where('email', $input['email'])->first();
        if (!empty($email)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '邮箱已经存在', []);
        }

        // 创建新用户
        $user = new AdminUser();
        $user->username = $input['username'];
        $user->email = $input['email'];
        $user->password = app('hash')->make($input['password']);

        DB::beginTransaction();

        if (!$user->save()) {
            DB::rollBack();
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '注册失败', $user);
        }

        // 处理角色的权限集合
        if (!empty($input['user_roles'])) {
            // 新增多出元素，和删除减少元素
            $user->userRoles()->sync($input['user_roles']);
        }

        DB::commit();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '注册成功', $user);
    }

    /**
     * @description: 业务，用户更新数据
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function edit($input)
    {
        DB::beginTransaction();

        $data = [
            'status' => $input['status'],
        ];
        $user = AdminUser::where('deleted_at', '=', NULL)->where('id', $input['id']);
        $result['update_num'] = $user->update($data);
        if (!$result['update_num']) {
            DB::rollBack();
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '更新失败', $result);
        }

        // 处理角色的权限集合
        if (!empty($input['user_roles'])) {
            // 新增多出元素，和删除减少元素
            $user->first()->userRoles()->sync($input['user_roles']);
        }

        DB::commit();
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '更新成功', $result);
    }

    /**
     * @description: 删除
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function delete($input)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];
        $update = AdminUser::where('deleted_at', '=', NULL)->whereIn('id', $input['id'])->update($data);
        $result['delete_num'] = $update;
        if (!$update) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $result);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $result);
    }
}
