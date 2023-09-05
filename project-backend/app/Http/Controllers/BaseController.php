<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    /**
     * @description: 输出给前端显示的格式
     * @Creator: Neo
     * @param {int} $code
     * @param {string} $status
     * @param {string} $message
     * @param {*} $data
     * @return {*}
     */
    static function output(int $code, string $status, string $message = '', $data = [])
    {
        if (gettype($data) != 'array' && gettype($data) != 'object') {
            return response()->json([
                'code' => '40002',
                'status' =>  'error',
                'message' => 'data需要是array或者object格式',
                'data' => $data,
            ]);
        }
        return response()->json([
            'code' => $code,
            'status' =>  $status,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * @description: curl获取数据
     * @Creator: Neo
     * @param {*} $method
     * @param {*} $url
     * @param {*} $json
     * @return {*}
     */
    static function clientUrl($method, $url, $json = [])
    {
        $client = new Client();
        $response = $client->request($method, $url, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
            ],
            'json' => $json
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * @description: 是否存在数据数组
     * @Creator: Neo
     * @param {*} $data
     * @param {*} $input
     * @param {*} $array
     * @return {*}
     */
    static function issetFieldArray($data, $input, $array)
    {
        foreach ($array as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }
        return $data;
    }

    /**
     * @description: 处理空白数组
     * @Creator: Neo
     * @param {*} $array
     * @param {*} $params
     * @return {*}
     */
    static function handleNullArray($array, $params = [])
    {
        if (empty($array)) {
            foreach ($params as $param) {
                $array[$param] = '';
            }
        }
        return $array;
    }

    /**
     * @description: 验证第一个失败时就返回
     * @author: Neo
     * @param {Request} $request
     * @param {array} $rules
     * @param {array} $messages
     * @return {*}
     */
    public function validatorStopOnFirst(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->input(), $rules, $messages);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            return $errors->first();
        }
        return null;
    }
}
