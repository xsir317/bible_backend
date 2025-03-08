<?php

namespace common\repository;


class ContentRepo extends BaseRepo
{
    // 支持多版本元数据
    const BOOKS = [
        'CUV' => [
            1 => ['name' => '创世纪' , 'chapters' => 50],
            2 => ['name' => '出埃及记' , 'chapters' => 40],
            3 => ['name' => '利未记' , 'chapters' => 27],
            4 => ['name' => '民数记' , 'chapters' => 36],
            5 => ['name' => '申命记' , 'chapters' => 34],
            6 => ['name' => '约书亚记' , 'chapters' => 24],
            7 => ['name' => '士师记' , 'chapters' => 21],
            8 => ['name' => '路得记' , 'chapters' => 4],
            9 => ['name' => '撒母耳记上' , 'chapters' => 31],
            10 => ['name' => '撒母耳记下' , 'chapters' => 24],
            11 => ['name' => '列王记上' , 'chapters' => 22],
            12 => ['name' => '列王记下' , 'chapters' => 25],
            13 => ['name' => '历代志上' , 'chapters' => 29],
            14 => ['name' => '历代志下' , 'chapters' => 36],
            15 => ['name' => '以斯拉记' , 'chapters' => 10],
            16 => ['name' => '尼希米记' , 'chapters' => 13],
            17 => ['name' => '以斯帖记' , 'chapters' => 10],
            18 => ['name' => '约伯记' , 'chapters' => 42],
            19 => ['name' => '诗篇' , 'chapters' => 150],
            20 => ['name' => '箴言' , 'chapters' => 31],
            21 => ['name' => '传道书' , 'chapters' => 12],
            22 => ['name' => '雅歌' , 'chapters' => 8],
            23 => ['name' => '以赛亚书' , 'chapters' => 66],
            24 => ['name' => '耶利米书' , 'chapters' => 52],
            25 => ['name' => '耶利米哀歌' , 'chapters' => 5],
            26 => ['name' => '以西结书' , 'chapters' => 48],
            27 => ['name' => '但以理书' , 'chapters' => 12],
            28 => ['name' => '何西阿书' , 'chapters' => 14],
            29 => ['name' => '约珥书' , 'chapters' => 3],
            30 => ['name' => '阿摩司书' , 'chapters' => 9],
            31 => ['name' => '俄巴底亚书' , 'chapters' => 1],
            32 => ['name' => '约拿书' , 'chapters' => 4],
            33 => ['name' => '弥迦书' , 'chapters' => 7],
            34 => ['name' => '那鸿书' , 'chapters' => 3],
            35 => ['name' => '哈巴谷书' , 'chapters' => 3],
            36 => ['name' => '西番雅书' , 'chapters' => 3],
            37 => ['name' => '哈该书' , 'chapters' => 2],
            38 => ['name' => '撒迦利亚书' , 'chapters' => 14],
            39 => ['name' => '玛拉基书' , 'chapters' => 4],
            40 => ['name' => '马太福音' , 'chapters' => 28],
            41 => ['name' => '马可福音' , 'chapters' => 16],
            42 => ['name' => '路加福音' , 'chapters' => 24],
            43 => ['name' => '约翰福音' , 'chapters' => 21],
            44 => ['name' => '使徒行传' , 'chapters' => 28],
            45 => ['name' => '罗马书' , 'chapters' => 16],
            46 => ['name' => '哥林多前书' , 'chapters' => 16],
            47 => ['name' => '哥林多后书' , 'chapters' => 13],
            48 => ['name' => '加拉太书' , 'chapters' => 6],
            49 => ['name' => '以弗所书' , 'chapters' => 6],
            50 => ['name' => '腓立比书' , 'chapters' => 4],
            51 => ['name' => '歌罗西书' , 'chapters' => 4],
            52 => ['name' => '帖撒罗尼迦前书' , 'chapters' => 5],
            53 => ['name' => '帖撒罗尼迦后书' , 'chapters' => 3],
            54 => ['name' => '提摩太前书' , 'chapters' => 6],
            55 => ['name' => '提摩太后书' , 'chapters' => 4],
            56 => ['name' => '提多书' , 'chapters' => 3],
            57 => ['name' => '腓利门书' , 'chapters' => 1],
            58 => ['name' => '希伯来书' , 'chapters' => 13],
            59 => ['name' => '雅各书' , 'chapters' => 5],
            60 => ['name' => '彼得前书' , 'chapters' => 5],
            61 => ['name' => '彼得后书' , 'chapters' => 3],
            62 => ['name' => '约翰一书' , 'chapters' => 5],
            63 => ['name' => '约翰二书' , 'chapters' => 1],
            64 => ['name' => '约翰三书' , 'chapters' => 1],
            65 => ['name' => '犹大书' , 'chapters' => 1],
            66 => ['name' => '启示录' , 'chapters' => 22],
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