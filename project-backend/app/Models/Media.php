<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'relative_link',
        'url',
        'upload_day',
        'size',
        'size_count',
        'media_tag_id',
        'user_id',
        'platform',
    ];

    // 媒体对应的分类
    public function bMediaCategory()
    {   
        return $this->belongsTo('App\Models\BMediaCategory', 'category_id', 'id');
    }

    // 作者
    public function mediaUser()
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
        $data = self::with(['mediaUser' => function ($query) use ($showArray) {
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
