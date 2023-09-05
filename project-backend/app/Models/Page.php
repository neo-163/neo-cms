<?php
/*
 * @Creator: Neo
 * @Date: 2022-09-16 15:03:24
 * @LastEditTime: 2022-10-04 13:30:56
 * @LastEditors: Neo let_to_code@163.com
 * @Description: 
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image',
        'content',
        'user_id',
        'url_tag',
        'json',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'json' => 'array',
    ];

    // 作者
    public function pageAuthor()
    {
        return $this->belongsTo('App\Models\AdminUser', 'user_id', 'id');
    }

    /**
     * @description: 列表+搜索
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        $showArray = $input['showArray'];
        $data = Page::with(['pageAuthor' => function ($query) use ($showArray) {
            $query->select($showArray);
        }]);

        if (!empty($input['title'])) {
            $data = $data->where('title', 'like', '%' . $input['title'] . '%');
        }
        if (!empty($input['status'])) {
            $data = $data->where('status', $input['status']);
        }

        // 关联查询，toArray()才能处理关联数组
        return $data->orderBy('id', $input['sort'])->paginate($input['limit'])->toArray();
    }
}
