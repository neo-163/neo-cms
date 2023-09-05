<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Article extends BaseModel
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

    // 文章封面图
    public function articleImage()
    {
        return $this->belongsTo('App\Models\BMedia', 'image', 'id');
    }

    // 作者
    public function articleAuthor()
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
        $data = self::with(['articleAuthor' => function ($query) use ($showArray) {
            $query->select($showArray);
        }]);

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
