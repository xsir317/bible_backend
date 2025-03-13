<?php

namespace api\components;

use yii\web\JsonResponseFormatter;

class EncryptFormatter extends JsonResponseFormatter
{
    public $contentType;

    public function format($response){
        if($response->data !== null){
            $content = json_encode($response->data);

            $encoded = ClientAuthHelper::encrypt($content);
            if($encoded){
                $this->contentType = self::CONTENT_TYPE_JSON;
                $response->getHeaders()->set('Content-Type', $this->contentType);

                $response->content = json_encode([
                    'encrypted_data' => $encoded
                ]);
            }
        }
    }

}