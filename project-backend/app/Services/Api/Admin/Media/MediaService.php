<?php

namespace App\Services\Api\Admin\Media;

use App\Http\Controllers\BaseController;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use App\PromptCode\SystemCode;

class MediaService extends BaseController
{
    /**
     * 页面列表，ASC：最早，DESC：最新
     * @author Neo
     * @param $input
     * @return mixed
     */
    public static function list($input)
    {
        if (empty($input['limit'])) {
            $input['limit'] = 20;
        }
        if (empty($input['sort']) || $input['sort'] != 'ASC') {
            $input['sort'] = 'DESC';
        }

        $input['showArray'] = ['id', 'username'];
        $data = Media::list($input);
        foreach ($data['data'] as &$articleList) {
            $articleList['media_user'] = self::handleNullArray($articleList['media_user'], $input['showArray']);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 详情
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    public static function details($input)
    {
        $data = Media::find($input['id']);
        if (empty($data)) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '找不到数据', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '获取成功', $data);
    }

    /**
     * @description: 创建
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */     
    public static function create($input)
    {
        $item = Media::where('url_tag', $input['url_tag'])->first();
        if ($item) {
            $tag['url_tag'] = $item['url_tag'];
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '页面链接标签已经存在', $tag);
        }

        $input['json'] = json_decode($input['json'], true);

        $data = [
            'title' => $input['title'],
            'image' => $input['image'] ?? '',
            'content' => $input['content'] ?? '',
            'url_tag' => $input['url_tag'],
            'json' => $input['json'] ?? [],
            'status' => $input['status'] ?? SystemCode::CODE_ARTICLE_DRAFT, // 状态：1-发布，2-草稿，3-回收
            'remarks' => $input['remarks'] ?? '',
        ];
        $create = Media::create($data);
        if (!$create) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '创建失败', $create);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '创建成功', $create);
    }

    /**
     * @description: 编辑
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    public static function edit($input)
    {
        $data = [
            'title' => $input['title'],
        ];
        $update = Media::where('id', $input['id'])->update($data);
        if (!$update) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '编辑失败', $data);
        }
        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '编辑成功', $data);
    }

    /**
     * @description: 上传到OSS接口
     * @author: Neo
     * @param {*} $request
     * @return {*}
     */
    public static function uploadOSSFile($request)
    {
        $size_count = $request->file('select_file')->getSize();

        if ($size_count < 1024) {
            $size = round($size_count, 2) . ' B';
        } elseif ($size_count < (1024 * 1024)) {
            $size = $size_count / 1024;
            $size = round($size, 2) . ' KB';
        } elseif ($size_count < (1024 * 1024 * 1024)) {
            $size = $size_count / (1024 * 1024);
            $size = round($size, 2) . ' MB';
        } else {
            $size = $size_count / (1024 * 1024 * 1024);
            $size = round($size, 2) . ' G';
        }

        $filename = $request->file('select_file')->getClientOriginalName();

        // 上传OSS核心代码 start
        $day = date('Y-m-d');
        $path = env('OSS_SPACE') . 'user_' . $request['adminUser']['id'] . '/' . date('Y-m') . '/' . $day;
        $file = $request->file('select_file');
        $filePath = self::uploadFile($path, $file);
        $url = env('OSS_MEDIA_URL') . "/" . $filePath;
        // 上传OSS核心代码 end

        // OSS上传是否成功做判断，失败返回信息

        $input['adminUser'] = $request['adminUser'];

        $data = [
            'title' => $filename,
            'relative_link' => $filePath, // 相对链接
            'url' => $url, // 初始绝对路径
            'upload_day' => $day, // 上传日期
            'size' => $size,
            'size_count' => $size_count,
            // 'category_id' => request('category_id'),
            'user_id' => $input['adminUser']['id'],
            'platform' => request('platform'),
        ];

        $media = Media::create($data);
        if (!$media) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '上传失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '上传成功', $data);
    }

    /**
     * @description: 上传到OSS接口
     * @author: Neo
     * @param {*} $request
     * @return {*}
     */
    public static function uploadLocal($request)
    {
        $day = date('Y-m-d');
        // return $request->file('select_file')->store("public/media/".date('Y-m'));

        $size_count = $request->file('select_file')->getSize();

        if ($size_count < 1024) {
            $size = round($size_count, 2) . ' B';
        } elseif ($size_count < (1024 * 1024)) {
            $size = $size_count / 1024;
            $size = round($size, 2) . ' KB';
        } elseif ($size_count < (1024 * 1024 * 1024)) {
            $size = $size_count / (1024 * 1024);
            $size = round($size, 2) . ' MB';
        } else {
            $size = $size_count / (1024 * 1024 * 1024);
            $size = round($size, 2) . ' G';
        }

        $extension = $request->file('select_file')->getClientOriginalExtension();
        $filename = $request->file('select_file')->getClientOriginalName();

        // $filenametostore = 'Media_'.date('Ymd_His').'_'.$filename;
        $filenametostore = 'Media_' . date('Ymd_His') . '.' . $extension;
        $file_link = '/public/media/' . $day . '/' . $filenametostore;
        Storage::put($file_link, fopen($request->file('select_file'), 'r+'), 'public');

        $input['adminUser'] = $request['adminUser'];

        $data = [
            'title' => $filename,
            'relative_link' => 'storage/media/' . $day . '/' . $filenametostore, // 相对链接
            'url' => $request->server('HTTP_HOST') . '/storage/media/' . $day . '/' . $filenametostore, // 初始绝对路径
            'upload_day' => $day, // 上传日期
            'size' => $size,
            'size_count' => $size_count,
            // 'media_tag_id' => request('media_tag_id'),
            'user_id' => $input['adminUser']['id'],
            'platform' => request('platform'),
        ];

        $media = Media::create($data);
        if (!$media) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '上传失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '上传成功', $data);
    }

    /**
     * @description: 文件上传到OSS
     * @author Neo
     * @param {*} $path
     * @param {*} $file
     * @return {*}
     */
    public static function uploadFile($path, $file)
    {
        $disk = Storage::disk('oss');
        $filePath = $disk->put($path, $file);
        return $filePath;
    }

    /**
     * @description: 可批量删除OSS媒体
     * @author Neo
     * @param {*} $input
     * @return {*}
     */
    public static function deleteOSSFile($input)
    {
        $media = Media::whereIn('id', $input['id']);
        $link = $media->pluck('relative_link')->toArray();

        $disk = Storage::disk('oss');
        $disk->delete($link);

        $data['delete_number'] = $media->delete();

        if ($data['delete_number'] == 0) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', $data);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', $data);
    }

    /**
     * @description: 单个删除本地媒体
     * @author: Neo
     * @param {*} $input
     * @return {*}
     */
    static function deleteLocal($input)
    {
        $media = Media::where('id', $input['id'])->first();

        $relative_link = 'storage/media/' . $media['upload_day'];
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative_link;
        $file = scandir($dir);

        foreach (array_reverse($file) as $file) {
            if ($relative_link . '/' . $file == $media['relative_link']) {
                $data = $_SERVER['DOCUMENT_ROOT'] . '/' . $media['relative_link'];
                unlink($data);
            }
        }

        if (!$media->delete()) {
            return self::output(SystemCode::CODE_OTHER_ERROR, SystemCode::STATUS_ERROR, '删除失败', []);
        }

        return self::output(SystemCode::CODE_SUCCESS, SystemCode::STATUS_SUCCESS, '删除成功', []);
    }
}
