<?php

return [
    // サムネイル画像の大きさ
    'SIZES'    => ['std', 'medium', 'high'],

    // genreをbattle, songに振り分けるための動画タイトルのキーワード
    'KEYWORDS' => [
        1  => [
            'radio' => ['”悩む”相談室'],
        ],
        2  => [
            'battle' => ['KOK', 'KING OF KINGS', 'SCHOOL OF RAP'],
            'others' => ['9SARI HEAD LINE', '漢たちとおさんぽ', 'あやなつさんほ', 'かんくんとあそぼ'],
        ],
        7  => [
            'others' => ['戌の散歩'],
        ],
        8  => [// 基本的にbattle
               'MV'        => ['【MV】', 'Music Video', 'MusicVideo'],
               'interview' => ['インタビュー', 'interview', '戦極最高会議'],
               'radio'     => ['BATTLEFIELD'],
        ],
        9  => [
            'MV'        => ['【MV】', 'Music Video', 'MusicVideo'],
            'interview' => ['チャンピオン', 'インタビュー', 'interview'],
        ],
        10 => [
            'interview' => ['INTERVIEW', 'オタク IN THA HOOD'],
        ],
        20 => [
            'others' => ['レゲエ'],
        ],
        21 => [// 基本的にnot HIPHOP($flag = 99)
               'MV' => ['SALU', 'AKLO', '湘南乃風'],
        ],
        23 => [
            'battle' => ['SPOTLIGHT', 'ENTER', 'MC BATTLE'],
        ],
        // 24 全部not HIPHOP
        24 => [
            'others' => ['日本の中学生', 'OYA'],
        ],
        29 => [ // 基本的に others
                'MV' => ['ちゃんみな -'],
        ],
        // 31 全部 others
        // 33 全部 interview
        37 => [ // 基本的にothers
                'MV' => ['Official Video'],
        ],
        38 => [
            'others' => ['バトル用ビート'],
        ],
        // 39 基本的に not HIPHOP
        39 => [
            'others' => ['HIPHOP'],
        ],
        41 => [
            'others' => ['メイキング'],
        ],
        42 => [
            'others' => ['フリースタイルダンジョン審査員'],
        ],
    ],

    'KEYWORD' => [
        'hiphop' => 'HIPHOP',
    ]
];
