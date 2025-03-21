<?php

namespace console\controllers;

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
}