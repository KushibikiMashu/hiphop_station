<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Video;
use App\Channel;
use App\VideoThumbnail;
use App\ChannelThumbnail;
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
     * channelのテストデータを1件作成する
     * $channelに返り値を代入する
     *
     * @return Channel
     */
    protected static function createChannelRecord(): Channel
    {
        return factory(Channel::class)->create();
    }

    /**
     * video, video_thumbnailのテストデータを1件ずつ作成する
     * [$video, $video_thumbnail]に返り値を代入する
     *
     * @return array
     */
    protected static function createVideoAndVideoThumbnailRecord(): array
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
     * channel, channel_thumbnailのテストデータを1件ずつ作成する
     * [$channel, $channel_thumbnail]に返り値を代入する
     *
     * @return array
     */
    protected static function createChannelAndChannelThumbnailRecord(): array
    {
        $channel = factory(Channel::class, 1)
            ->create()
            ->each(function ($channel) {
                factory(ChannelThumbnail::class, 1)
                    ->make()
                    ->each(function ($channel_thumbnail) use ($channel) {
                        $channel->channel_thumbnail()->save($channel_thumbnail);
                    });
            });
        $channel_thumbnail = ChannelThumbnail::where('channel_id', $channel[0]->id)->get();
        return [$channel[0], $channel_thumbnail[0]];
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
