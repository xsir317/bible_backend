<?php

namespace common\components\llm\adapters;

use GuzzleHttp\Psr7\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TencentHunyuanAdapter
{
    const API_ENDPOINT = 'https://hunyuan.tencent.com/api/v3/chat/completions';

    public function execute($prompt, $params) {
        $client = new Client();

        try {
            $response = $client->post(self::API_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . \Yii::$app->params['tencentApiKey'],
                    'X-AppId' => \Yii::$app->params['tencentAppId']
                ],
                'json' => [ // 自动处理 JSON 编码和 Content-Type
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'stream' => false,
                    'model_params' => array_merge([
                        'temperature' => 0.8,
                        'max_tokens' => 1500
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
            return $this->parseHunyuanResponse($responseData);

        } catch (RequestException $e) {
            // 处理网络级错误（如连接超时）
            throw new \Exception("网络请求异常: " . $e->getMessage(), 5002);
        }

        return null;
    }

    private function parseHunyuanResponse($data) {
        return [
            'output' => [
                'text' => $data['choices'][0]['message']['content'] ?? ''
            ],
            'usage' => [
                'total_tokens' => $data['usage']['total_tokens'] ?? 0
            ]
        ];
    }
}