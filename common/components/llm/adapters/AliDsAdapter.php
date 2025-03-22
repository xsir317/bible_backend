<?php

namespace common\components\llm\adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AliDsAdapter extends Adapter
{
    const ENDPOINT = 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation';
    //参考 https://help.aliyun.com/zh/model-studio/developer-reference/deepseek?spm=a2c4g.11186623.help-menu-2400256.d_3_3_1.3cea47bb8RnMlm#3f350ac2c3is6
    //ali 不同的模型输入格式也不同， 必须分拆不同的 Adapter

    public function execute($prompt, $params , $model = 'deepseek-r1') {
        $client = new Client();
        if(empty($model)){
            $model = 'deepseek-r1';
        }

        try {
            $response = $client->post(self::ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . getenv('ALI_DASHSCOPE_API_KEY'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => $model,
                    //'model' => 'deepseek-r1-distill-llama-70b',
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
            //格式化为 基类 normalizeResponse 要求的格式：
            $text = '';
            if(!empty($responseData['choices']) && !empty($responseData['choices'][0]['message'])){
                $text = $responseData['choices'][0]['message']['content'] ?? '';
            }
            return [
                'usage' => $responseData['usage'] ?? [],
                'output' => [
                    'text' => $text
                ]
            ];

        } catch (RequestException $e) {
            // 处理网络级错误（如连接超时）
            throw new \Exception("网络请求异常: " . $e->getMessage(), 5002);
        }

        return null;
    }
}