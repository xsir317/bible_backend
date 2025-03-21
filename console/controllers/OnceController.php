<?php

namespace console\controllers;

use common\models\BibleExplanations;
use common\models\BiblePassages;
use common\models\BibleVerses;
use common\repository\ContentRepo;
use yii\console\Controller;

class OnceController extends Controller
{
    public function actionExplain(){
        foreach (ContentRepo::BOOKS['CUV'] as $k => $row){
            for($i = 1;$i<=$row['chapters']; $i++){
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
如下内容是《圣经》的 《{$row['name']}》的第{$i} 章，请按照各小节的含义分段，并分别讲解每一段。讲解内容包括：
1. 历史背景解释
2. 关键词语解析
3. 属灵应用建议

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


[限制]
- 解释范围限定在标记的上下文内
- 避免过度引申
- 保持教派中立

[经文]
{$contents}
EOF;
                echo $content;exit;
            }
        }
    }

    private function saveLLMResponse($response ,$version , $book_id , $chapter ,$model_name){
        $lang = 'zh-cn';
        $decode = @json_decode($response);
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