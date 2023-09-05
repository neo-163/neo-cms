<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class MediaCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'sort',
        'user_id',
    ];

    // 创建者
    public function mediaCategoryCreator()
    {
        return $this->belongsTo('App\Models\AdminUser', 'user_id', 'id');
    }

    /**
     * @description: 媒体分类有多少个媒体
     * @author: Neo
     * @return {*}
     */
    public function mediaNumber()
    {
        // return $this->hasMany(App\Models\Media::class, 'media_category_id', 'id');
    }

    /**
     * @description: 列表，媒体分类有多少个媒体
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function list($input)
    {
        // $data = self::withCount('mediaNumber');

        // return $data->orderBy('id', $input['sort'])->paginate($input['limit'])->toArray();
    }
}
