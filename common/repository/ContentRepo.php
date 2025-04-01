<?php

namespace common\repository;


class ContentRepo extends BaseRepo
{
    // 支持多版本元数据
    const BOOKS = [
        'CUV' => [
            1 => ['name' => '创世纪' , 'short' => '创',  'chapters' => 50],
            2 => ['name' => '出埃及记' , 'short' => '出',  'chapters' => 40],
            3 => ['name' => '利未记' , 'short' => '利',  'chapters' => 27],
            4 => ['name' => '民数记' , 'short' => '民',  'chapters' => 36],
            5 => ['name' => '申命记' , 'short' => '申',  'chapters' => 34],
            6 => ['name' => '约书亚记' , 'short' => '书',  'chapters' => 24],
            7 => ['name' => '士师记' , 'short' => '士',  'chapters' => 21],
            8 => ['name' => '路得记' , 'short' => '得',  'chapters' => 4],
            9 => ['name' => '撒母耳记上' , 'short' => '撒上',  'chapters' => 31],
            10 => ['name' => '撒母耳记下' , 'short' => '撒下',  'chapters' => 24],
            11 => ['name' => '列王记上' , 'short' => '王上',  'chapters' => 22],
            12 => ['name' => '列王记下' , 'short' => '王下',  'chapters' => 25],
            13 => ['name' => '历代志上' , 'short' => '代上',  'chapters' => 29],
            14 => ['name' => '历代志下' , 'short' => '代下',  'chapters' => 36],
            15 => ['name' => '以斯拉记' , 'short' => '拉',  'chapters' => 10],
            16 => ['name' => '尼希米记' , 'short' => '尼',  'chapters' => 13],
            17 => ['name' => '以斯帖记' , 'short' => '斯',  'chapters' => 10],
            18 => ['name' => '约伯记' , 'short' => '伯',  'chapters' => 42],
            19 => ['name' => '诗篇' , 'short' => '诗',  'chapters' => 150],
            20 => ['name' => '箴言' , 'short' => '箴',  'chapters' => 31],
            21 => ['name' => '传道书' , 'short' => '传',  'chapters' => 12],
            22 => ['name' => '雅歌' , 'short' => '歌',  'chapters' => 8],
            23 => ['name' => '以赛亚书' , 'short' => '赛',  'chapters' => 66],
            24 => ['name' => '耶利米书' , 'short' => '耶',  'chapters' => 52],
            25 => ['name' => '耶利米哀歌' , 'short' => '哀',  'chapters' => 5],
            26 => ['name' => '以西结书' , 'short' => '结',  'chapters' => 48],
            27 => ['name' => '但以理书' , 'short' => '但',  'chapters' => 12],
            28 => ['name' => '何西阿书' , 'short' => '何',  'chapters' => 14],
            29 => ['name' => '约珥书' , 'short' => '珥',  'chapters' => 3],
            30 => ['name' => '阿摩司书' , 'short' => '摩',  'chapters' => 9],
            31 => ['name' => '俄巴底亚书' , 'short' => '俄',  'chapters' => 1],
            32 => ['name' => '约拿书' , 'short' => '拿',  'chapters' => 4],
            33 => ['name' => '弥迦书' , 'short' => '弥',  'chapters' => 7],
            34 => ['name' => '那鸿书' , 'short' => '鸿',  'chapters' => 3],
            35 => ['name' => '哈巴谷书' , 'short' => '哈',  'chapters' => 3],
            36 => ['name' => '西番雅书' , 'short' => '番',  'chapters' => 3],
            37 => ['name' => '哈该书' , 'short' => '该',  'chapters' => 2],
            38 => ['name' => '撒迦利亚书' , 'short' => '亚',  'chapters' => 14],
            39 => ['name' => '玛拉基书' , 'short' => '玛',  'chapters' => 4],
            40 => ['name' => '马太福音' , 'short' => '太',  'chapters' => 28],
            41 => ['name' => '马可福音' , 'short' => '可',  'chapters' => 16],
            42 => ['name' => '路加福音' , 'short' => '路',  'chapters' => 24],
            43 => ['name' => '约翰福音' , 'short' => '约',  'chapters' => 21],
            44 => ['name' => '使徒行传' , 'short' => '徒',  'chapters' => 28],
            45 => ['name' => '罗马书' , 'short' => '罗',  'chapters' => 16],
            46 => ['name' => '哥林多前书' , 'short' => '林前',  'chapters' => 16],
            47 => ['name' => '哥林多后书' , 'short' => '林后',  'chapters' => 13],
            48 => ['name' => '加拉太书' , 'short' => '加',  'chapters' => 6],
            49 => ['name' => '以弗所书' , 'short' => '弗',  'chapters' => 6],
            50 => ['name' => '腓立比书' , 'short' => '腓',  'chapters' => 4],
            51 => ['name' => '歌罗西书' , 'short' => '西',  'chapters' => 4],
            52 => ['name' => '帖撒罗尼迦前书' , 'short' => '帖前',  'chapters' => 5],
            53 => ['name' => '帖撒罗尼迦后书' , 'short' => '帖后',  'chapters' => 3],
            54 => ['name' => '提摩太前书' , 'short' => '提前',  'chapters' => 6],
            55 => ['name' => '提摩太后书' , 'short' => '提后',  'chapters' => 4],
            56 => ['name' => '提多书' , 'short' => '多',  'chapters' => 3],
            57 => ['name' => '腓利门书' , 'short' => '门',  'chapters' => 1],
            58 => ['name' => '希伯来书' , 'short' => '来',  'chapters' => 13],
            59 => ['name' => '雅各书' , 'short' => '雅',  'chapters' => 5],
            60 => ['name' => '彼得前书' , 'short' => '彼前',  'chapters' => 5],
            61 => ['name' => '彼得后书' , 'short' => '彼后',  'chapters' => 3],
            62 => ['name' => '约翰一书' , 'short' => '约壹',  'chapters' => 5],
            63 => ['name' => '约翰二书' , 'short' => '约贰',  'chapters' => 1],
            64 => ['name' => '约翰三书' , 'short' => '约叁',  'chapters' => 1],
            65 => ['name' => '犹大书' , 'short' => '犹',  'chapters' => 1],
            66 => ['name' => '启示录' , 'short' => '启',  'chapters' => 22],
        ],
    ];

    // 获取指定版本的书卷信息
    public static function getBookMeta(string $version, int $bookId): ?array {
        return self::BOOKS[$version][$bookId] ?? null;
    }

    // 获取章节总数校验
    public static function validateChapter(string $version, int $bookId, int $chapter): bool {
        $meta = self::getBookMeta($version, $bookId);
        return $meta && $chapter >= 1 && $chapter <= $meta['chapters'];
    }
}