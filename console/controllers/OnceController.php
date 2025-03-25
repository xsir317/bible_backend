<?php

namespace console\controllers;

use common\components\llm\LLMService;
use common\models\BibleExplanations;
use common\models\BiblePassages;
use common\models\BibleVerses;
use common\repository\ContentRepo;
use yii\console\Controller;

class OnceController extends Controller
{
    public function actionExplain(){
        $llm = new LLMService();
        foreach (ContentRepo::BOOKS['CUV'] as $k => $row){
            $exit_mark = \Yii::$app->getRuntimePath().'/exit.lock';
            if(file_exists($exit_mark)){
                return;
            }
            if($k <= 40 ) continue;
            for($i = 1;$i<=$row['chapters']; $i++){
                //如果已有 passages ，跳过
                $exist_passage = BiblePassages::find()
                    ->where([
                        'version' => 'CUV',
                        'book_id' => $k,
                        'chapter_num' => $i,
                    ])
                    ->count();
                if($exist_passage){
                    continue;
                }
                $verses = BibleVerses::find()
                    ->where(['book_id' => $k , 'chapter_num' => $i])
                    ->all();
                $contents = '';
                foreach ($verses as $verse){
                    $contents .= "{$verse->verse_num}. {$verse->content}\n";
                }
                $contents = trim($contents);

                $content = <<<EOF
[角色]
你是一位资深的圣经学者

[任务]
如下内容是《圣经》的 《{$row['name']}》的第{$i}章，请按照各小节的含义分段，并分别讲解每一段。讲解内容应包括属灵应用的建议。

如果你认为这些内容确实应该属于同一部分也没问题，不必强行分段。

[输出格式]
请以如下json 格式输出：
```
[
	{
		"verse_start": 1,
		"verse_end":3,
		"explain":"关于第1，2，3小节的解释"
	},
	{
		"verse_start": 4,
		"verse_end":8,
		"explain":"关于第4,5,6,7,8小节的解释"
	}
]
```
不要输出json以外的任何字符，以方便后续的程序解析。


[限制]
- 保持教派中立

[经文]
{$contents}
EOF;
                echo "call LLM for book {$k} , chapter {$i}\n";
                $response = $llm->generate(LLMService::MODEL_ALI_QWEN , $content,[],'qwen-plus-2025-01-25');
                if(!empty($response['data'])){
                    $this->saveLLMResponse($response['data']['content'] , 'CUV', $k , $i , 'qwen-plus-2025-01-25');
                }
                echo "code : {$response['code']} \n";
            }
        }
    }

    public function actionFix($book_id , $chapter_id){
        $verses = BibleVerses::find()
            ->where(['book_id' => $book_id , 'chapter_num' => $chapter_id])
            ->all();
        $row = ContentRepo::BOOKS['CUV'][$book_id];
        $contents = '';
        foreach ($verses as $verse){
            $contents .= "{$verse->verse_num}. {$verse->content}\n";
        }
        $contents = trim($contents);

        $content = <<<EOF
[角色]
你是一位资深的圣经学者

[任务]
如下内容是《圣经》的 《{$row['name']}》的第{$chapter_id}章，请按照各小节的含义分段，并分别讲解每一段。讲解内容应包括属灵应用的建议。

如果你认为这些内容确实应该属于同一部分也没问题，不必强行分段。

[输出格式]
请以如下json 格式输出：
```
[
{
"verse_start": 1,
"verse_end":3,
"explain":"关于第1，2，3小节的解释"
},
{
"verse_start": 4,
"verse_end":8,
"explain":"关于第4,5,6,7,8小节的解释"
}
]
```
不要输出json以外的任何字符，以方便后续的程序解析。

[限制]
- 保持教派中立

[经文]
{$contents}
EOF;
        $llm = new LLMService();
        $response = $llm->generate(LLMService::MODEL_ALI_QWEN , $content,[],'qwen-plus');
        var_dump($response);
        if(!empty($response['data'])){
            $this->saveLLMResponse($response['data']['content'] , 'CUV', $book_id , $chapter_id , 'qwen-plus');
        }
        if($response['code'] != 200){
            echo "code : {$response['code']} \n";
        }
    }

    public function actionSave(){
        $this->saveLLMResponse('','CUV', 20 , 8 , 'qwen-plus');
    }

    private function saveLLMResponse($response ,$version , $book_id , $chapter ,$model_name){
        $lang = 'zh-cn';
        $decode = @json_decode($response,1);
        if(empty($decode)){
            echo $response;
            echo "\nbook {$book_id} chapter {$chapter} \n";
            exit;
        }
        foreach ($decode as $item){
            if(empty($item['explain'])) continue;
            //找对应的passage
            $passage = BiblePassages::find()
                ->where([
                    'version' => $version,
                    'book_id' => $book_id,
                    'chapter_num' => $chapter,
                    'start_verse' => $item['verse_start'],
                    'end_verse' =>  $item['verse_end'],
                ])
                ->limit(1)
                ->one();
            if(!$passage){
                $passage = new BiblePassages();
                $passage->version = $version;
                $passage->book_id = $book_id;
                $passage->chapter_num = $chapter;
                $passage->start_verse =  $item['verse_start'];
                $passage->end_verse = $item['verse_end'];
                $passage->token_count = 0;//TODO 这个暂时不重要，以后再说
                $passage->save();
            }
            //找解释 , TODO 前期只搞中文的，以后再考虑别的。
            $explain = BibleExplanations::find()->where(['passage_id' => $passage->id , 'lang' => $lang])
                ->limit(1) ->one();
            if(!$explain){
                $explain = new BibleExplanations();
                $explain->passage_id = $passage->id;
                $explain->lang = $lang;
                $explain->model = $model_name;
                $explain->content = $item['explain'];
                $explain->context_verses = "{$passage->start_verse}-{$passage->end_verse}";
                $explain->updated_at = date('Y-m-d H:i:s');
                $explain->save();
            }
        }
    }
}