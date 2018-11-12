<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Video;
use App\VideoThumbnail;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * ReflectionClassでprivateメソッドを呼び出す
     *
     * @param $class
     * @param string $method
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected static function callPrivateMethod($class, string $method): \ReflectionMethod
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * videoのテストデータを1件作成する
     * $videoに返り値を代入する
     *
     * @return Video
     */
    protected static function createVideoRecord(): Video
    {
        return factory(Video::class)->create();
    }

    /**
     * video, video_thumbnailのテストデータを1件ずつ作成する
     * list($video, $video_thumbnail)に返り値を代入する
     *
     * @return array
     */
    protected static function createVideoAndVideoThumnailRecord(): array
    {
        $video = factory(Video::class, 1)
            ->create()
            ->each(function ($video) {
                factory(VideoThumbnail::class, 1)
                    ->make()
                    ->each(function ($video_thumbnail) use ($video) {
                        $video->video_thumbnail()->save($video_thumbnail);
                    });
            });
        $video_thumbnail = VideoThumbnail::where('video_id', $video[0]->id)->get();
        return [$video[0], $video_thumbnail[0]];
    }

    /**
     * テーブル名とidでレコードを削除する
     *
     * @param string $table
     * @param int $id
     */
    protected static function deleteRecordByTableAndId(string $table, int $id): void
    {
        DB::table($table)->where('id', $id)->delete();
    }
}
