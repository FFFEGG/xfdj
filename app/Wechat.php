<?php


namespace App;


use EasyWeChat\Factory;

class Wechat extends Base
{
    public static function msg()
    {
        $config = [
            'app_id' => 'wxed65c3911947e645',
            'secret' => '6bac2b4155da598e2ba423a17b6ad471',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat.log',
            ],
        ];
        return Factory::miniProgram($config);
    }
}
