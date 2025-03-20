<?php

namespace common\components\llm\adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AliQwenAdapter extends Adapter
{
    const API_ENDPOINT = 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation';

    public function execute($prompt, $params) {
        $client = new Client();

        try {
            $response = $client->post(self::API_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . \Yii::$app->params['aliApiKey'],
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'qwen-max',
                    'input' => [
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ]
                    ],
                    'parameters' => array_merge([
                        'temperature' => 0.7,
                        'max_tokens' => 1000
                    ], $params)
                ],
                'http_errors' => false // 禁用自动抛出 4xx/5xx 异常
            ]);

            // 手动检查状态码
            if ($response->getStatusCode() !== 200) {
                $errorContent = $response->getBody()->getContents();
                throw new \Exception("API请求失败: " . $errorContent, 5002);
            }

            // 处理成功响应
            $responseData = json_decode($response->getBody()->getContents(), true);
            return $responseData;

        } catch (RequestException $e) {
            // 处理网络级错误（如连接超时）
            throw new \Exception("网络请求异常: " . $e->getMessage(), 5002);
        }

        return null;
    }
}