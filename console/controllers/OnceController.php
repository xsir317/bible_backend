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
        foreach (ContentRepo::BOOKS['CUV'] as $k => $row){
            $exit_mark = \Yii::$app->getRuntimePath().'/exit.lock';
            if(file_exists($exit_mark)){
                return;
            }
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
如下内容是《圣经》的 《{$row['name']}》的第{$i} 章，请按照各小节的含义分段，并分别讲解每一段。讲解内容应包括属灵应用的建议。

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
- 保持教派中立

[经文]
{$contents}
EOF;
                $llm = new LLMService();
                $response = $llm->generate(LLMService::MODEL_ALI_QWEN , $content,[],'qwen-plus');
                var_dump($response);
                if(!empty($response['data'])){
                    $this->saveLLMResponse($response['data']['content'] , 'CUV', $k , $i , 'qwen-plus');
                }
                echo $response['data']['content'],"####\n\n";
            }
        }
    }

    public function actionSave(){
        $json = <<<JSN
[
    {
        "verse_start": 1,
        "verse_end": 3,
        "explain": "整体含义：这段经文描述了宇宙的起源，强调神是创造者，天地万物都是祂的创造。起初，地是空虚混沌的，神的灵运行在水面上，表明神的主权和创造的能力。神说‘要有光’，光就出现了，这显示了神的话语具有创造的力量。\\n关键词语解析：‘起初’表示时间的开始；‘创造’意味着从无到有的行动；‘神的灵运行’表明神的同在和工作；‘神说’显示了神的话语的权威和力量。\\n属灵应用建议：这段经文提醒我们，神是万物的创造者，我们应该敬畏祂，并相信祂的话语具有改变和创造的力量。"
    },
    {
        "verse_start": 4,
        "verse_end": 8,
        "explain": "整体含义：这段经文描述了神对光的创造和分离。神看光是好的，并将光与暗分开，称光为昼，暗为夜。接着，神创造了空气，将水分开，称空气为天。\\n关键词语解析：‘光’象征神的启示和真理；‘分开’表示神的秩序和界限；‘空气’（天）代表神的创造和维持。\\n属灵应用建议：这段经文教导我们，神是秩序和界限的设立者，我们应该在祂的旨意中生活，并寻求祂的光和真理。"
    },
    {
        "verse_start": 9,
        "verse_end": 13,
        "explain": "整体含义：这段经文描述了神对陆地和海洋的创造。神命令水聚在一处，使旱地露出来，并称旱地为地，水的聚处为海。接着，神命令地长出植物，各从其类，神看着是好的。\\n关键词语解析：‘旱地’和‘海’代表神创造的多样性和丰富性；‘各从其类’显示神的创造是有秩序和规律的。\\n属灵应用建议：这段经文提醒我们，神创造的世界是丰富和有序的，我们应该珍惜和保护祂的创造，并在其中找到祂的智慧和美善。"
    },
    {
        "verse_start": 14,
        "verse_end": 19,
        "explain": "整体含义：这段经文描述了神对光体的创造。神命令天上要有光体，分别昼夜，作记号，定节令、日子和年岁。神造了两个大光（太阳和月亮）和众星，并将它们摆列在天空，普照在地上。\\n关键词语解析：‘光体’象征神的引导和启示；‘分别’显示神的秩序和规律；‘摆列’表明神的安排和设计。\\n属灵应用建议：这段经文教导我们，神是时间和季节的主宰，我们应该在祂的引导下生活，并依靠祂的启示和智慧。"
    },
    {
        "verse_start": 20,
        "verse_end": 23,
        "explain": "整体含义：这段经文描述了神对海洋生物和飞鸟的创造。神命令水滋生有生命的物，并造出大鱼和各样水中生物，以及各样飞鸟。神看着是好的，并赐福给它们，命令它们滋生繁多。\\n关键词语解析：‘有生命的物’显示神的创造是充满生机的；‘各从其类’表明神的创造是有秩序和规律的；‘赐福’表示神的恩典和祝福。\\n属灵应用建议：这段经文提醒我们，神是生命的赐予者，我们应该感谢祂的创造和祝福，并在祂的恩典中生活。"
    },
    {
        "verse_start": 24,
        "verse_end": 31,
        "explain": "整体含义：这段经文描述了神对陆地动物和人类的创造。神命令地生出活物，各从其类，并造出野兽、牲畜和昆虫。接着，神按照自己的形像造人，赐福给他们，并命令他们生养众多，治理全地。神看着一切所造的都甚好。\\n关键词语解析：‘活物’显示神的创造是充满生机的；‘各从其类’表明神的创造是有秩序和规律的；‘形像’表示人类与神的相似性和关系；‘赐福’表示神的恩典和祝福。\\n属灵应用建议：这段经文教导我们，人类是神按照祂的形像创造的，具有特殊的意义和价值。我们应该在神的旨意中生活，并履行治理和保护祂的创造的职责。"
    }
]
JSN;
    $this->saveLLMResponse($json , 'CUV', 1 , 1 , 'deepseek-r1');
    }
    private function saveLLMResponse($response ,$version , $book_id , $chapter ,$model_name){
        $lang = 'zh-cn';
        $decode = @json_decode($response,1);
        if(empty($decode)){
            echo $response;exit;
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