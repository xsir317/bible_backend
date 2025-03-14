<?php

namespace common\repository;

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

class EmailRepo extends BaseRepo
{
    public static function send($sender_email,$to_email, $title, $body, $is_html){
        $SesClient = new SesClient([
            'version' => 'latest',
            'region' => 'ap-northeast-1',
            'timeout' => YII_ENV_PROD ? 5 : 20,
        ]);
        $recipient_emails = [$to_email];
        //$configuration_set = 'ConfigSet';

        $char_set = 'UTF-8';

        try {
            $format = $is_html ? 'Html':'Text';
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'ReplyToAddresses' => [$sender_email],
                'Source' => $sender_email,
                'Message' => [
                    'Body' => [
                        $format => [
                            'Charset' => $char_set,
                            'Data' => $body,
                        ]
                    ],
                    'Subject' => [
                        'Charset' => $char_set,
                        'Data' => $title,
                    ],
                ],
                //'ConfigurationSetName' => $configuration_set,
            ]);
            return $result['MessageId'];
        } catch (AwsException $e) {
            if(!YII_ENV_PROD){
                Yii::error([
                    'body' => $body ,
                    'to' => $to_email,
                    'result' => $e->getAwsErrorMessage()
                ],'email');
            }
            return self::_err($e->getAwsErrorMessage());
        }
    }

    public static function sendVerifyCode($email,$type='reg',$ip = '')
    {
        if(!$email)
        {
            return self::_err('手机号为空');
        }
        $code = sprintf("%06d",rand(100,999999));
        \Yii::$app->cache->set(self::cache_key($email,$type),$code,600);
        if(1){
            return $code;
        }
        return self::send($email,$type,$code,'',$ip);
    }
    public static function checkVerifyCode($email,$code,$type)
    {
        $origin_code = \Yii::$app->cache->get(self::cache_key($email,$type));
        \Yii::$app->cache->delete(self::cache_key($email,$type));
        return $code && ($origin_code == $code);
    }

    private static function cache_key($email,$type)
    {
        return sprintf("vcode::%s:%s",$email,$type);
    }
}