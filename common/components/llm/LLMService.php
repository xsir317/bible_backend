<?php
namespace common\components\llm;

use common\components\llm\adapters\Adapter;
use common\repository\LogRepo;
use yii\base\Component;

/**
 * 统一大模型调用管理类
 */
class LLMService extends Component
{
    // 支持模型类型常量
    const MODEL_ALI_QWEN = 'ali_qwen';
    const MODEL_TENCENT_HUNYUAN = 'tencent_hunyuan';

    // 统一响应格式
    public static function formatResponse($code, $message, $data = []) {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ];
    }

    // 模型适配器映射
    private $adapterMap = [
        self::MODEL_ALI_QWEN => 'AliQwenAdapter',
        self::MODEL_TENCENT_HUNYUAN => 'TencentHunyuanAdapter',
    ];

    // 统一调用入口
    public function generate($modelType, $prompt, $params = []) {
        try {
            if (!isset($this->adapterMap[$modelType])) {
                throw new \Exception("Unsupported model type");
            }

            $adapterClass = __NAMESPACE__ . '\\adapters\\' . $this->adapterMap[$modelType];
            /**
             * @var $adapter Adapter
             */
            $adapter = new $adapterClass();

            // 统一输入预处理
            $processedPrompt = $this->preprocessPrompt($prompt);

            // 执行调用
            $rawResponse = $adapter->execute($processedPrompt, $params);

            // 统一响应处理
            return $this->formatResponse(
                200,
                'Success',
                $this->normalizeResponse($rawResponse)
            );
        } catch (\Exception $e) {
            return $this->formatResponse(
                $e->getCode() ?: 500,
                $e->getMessage()
            );
        }
    }

    // 输入预处理（圣经场景优化）
    private function preprocessPrompt($prompt) {
        // 添加圣经解读专用提示词
        return "你是一个圣经研究专家，请用中文回答以下问题：\n" . $prompt;
    }

    // 响应标准化
    private function normalizeResponse($rawData) {
        LogRepo::file_log( \Yii::$app->getRuntimePath() . '/post.log' , json_encode($rawData));
        return [
            'content' => $rawData['output']['text'] ?? '',
            'usage' => [
                'total_tokens' => $rawData['usage']['total_tokens'] ?? 0,
                'input_tokens' => $rawData['usage']['input_tokens'] ?? 0,
                'output_tokens' => $rawData['usage']['output_tokens'] ?? 0,
            ],
            'model_metadata' => $rawData['metadata'] ?? []
        ];
    }
}