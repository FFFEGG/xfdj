<?php


namespace App;


use Overtrue\EasySms\EasySms;

class Msgs extends Base
{
    public static function sendmsg($type,$tel,$str="")
    {

        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => 'LTAIjDjicYZ2qVRV',
                    'access_key_secret' => 'dOsxHsBKhwaQPyb9H4LfedcCuYDFCI',
                    'sign_name' => '幸福家家',
                ],
            ],
        ];
        $easySms = new EasySms($config);

        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = "http://api.smsbao.com/";
        $user = "xfdj"; //短信平台帐号
        $pass = md5("xfdj123654."); //短信平台密码
        switch ($type) {
            case 1:
                $content="发货通知，您购买的".$str."正在急速配送中!";//要发送的短信内容
                $easySms->send($tel, [
                    'content'  => $content,
                    'template' => 'SMS_168306207',
                    'data' => [
                        'name' => $str
                    ],
                ]);
                break;
            case 2:
                $content="您购买的".$str."已到达提货点，赶紧去提货吧！"; //要发送的短信内容
                $easySms->send($tel, [
                    'content'  => $content,
                    'template' => 'SMS_168306214',
                    'data' => [
                        'name' => $str
                    ],
                ]);
                break;
            case 3:
                $time = date('Y-m-d H:i:s');
                $content="提货成功，您购买的".$str."已经提货，提货时间".$time; //要发送的短信内容
                $easySms->send($tel, [
                    'content'  => $content,
                    'template' => 'SMS_168306218',
                    'data' => [
                        'name' => $str,
                        'time' => $time
                    ],
                ]);
                break;
            case 4:
                $code = rand(1000,9999);
                $content="您的验证码位".$code; //要发送的短信内容
                MsgCode::create([
                    'tel'=>$tel,
                    'code'=>$code
                ]);
                $easySms->send($tel, [
                    'content'  => $content,
                    'template' => 'SMS_168311095',
                    'data' => [
                        'code' => $code
                    ],
                ]);
                break;
            case 5:
                $content="恭喜您，您的提货点资料审核已经通过"; //要发送的短信内容
                $easySms->send($tel, [
                    'content'  => $content,
                    'template' => 'SMS_168311101',
                ]);
                break;
        }
        $phone = $tel; //要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;

    }
}
