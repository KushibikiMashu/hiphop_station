<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ApiRepository;

class NewVideoFetcherService
{
    private $channel_repo;
    private $video_repo;
    private $video_thumbnail_repo;
    private $api_repo;

    const format = \DateTime::ATOM;

    public function __construct
    (
        ChannelRepository $channel_repo,
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        ApiRepository $api_repo
    )
    {
        $this->channel_repo = $channel_repo;
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->api_repo = $api_repo;
    }

    /**
     * commandから呼び出す
     *
     */
    public function run(): void
    {
//        video_countとvideoの数が一致しなければreturn
//        count <= video return
        $this->getNewChannelHash();
    }

    /**
     * 紐づくvideoがないchannelのhashを取得する
     */
    private function getNewChannelHash(): void
    {
        foreach ($this->channel_repo->fetchAll() as $record) {
            $this->getNewVideosByChannel($record);
        }
    }

    /**
     * channelレコードを受け取って、published_atから現在に至るまでそのchannelに紐づく動画を全て取得する
     *
     * @param $channel
     * @throws \Exception
     */
    private function getNewVideosByChannel($channel): void
    {
        if (0 < $channel->video_count && $channel->video_count <= 50) {
            $this->saveNewChannelUnderFiftyVideos($channel);
            return;
        }

        $now_time = strtotime('now');
        $pub_time = strtotime($channel->published_at);
        while ($pub_time < $now_time) {
            $start = $this->convertToYoutubeDatetimeFormat($pub_time);
            $end = $this->AddOneWeek($pub_time);
            // startが現在時刻を超えたら、現在時刻を利用する
            if ($now_time < strtotime($end)) {
                $end = $this->convertToYoutubeDatetimeFormat($now_time);
            }
            dump($end); // あえて入れている
            [$videos, $video_thumbnails] = $this->api_repo->getNewVideosByChannelHash($channel->id, $channel->hash, 50, $start, $end);
            $pub_time += 86400 * 7;
            if (is_null($videos)) continue;
            $this->saveVideosAndThumbnails($videos, $video_thumbnails);
        }
    }

    /**
     * 動画の数が50以下のchannelを登録する
     *
     * @param $channel
     */
    private function saveNewChannelUnderFiftyVideos($channel): void
    {
        [$videos, $video_thumbnails] = $this->api_repo->getNewVideosByChannelHashUnderFiftyVideos($channel->id, $channel->hash, 50);
        $this->saveVideosAndThumbnails($videos, $video_thumbnails);
    }

    /**
     * タイムスタンプを受け取ってYouTubeAPIのフォーマットを作成する
     *
     * @param $timestamp
     * @return string
     */
    private function convertToYoutubeDatetimeFormat($timestamp): string
    {
        return substr(\Carbon\Carbon::createFromTimestamp($timestamp)->format(self::format), 0, 19) . '.000Z';
    }

    /**
     * タイムスタンプを受け取って１週間プラスする
     *
     * @param $timestamp
     * @return string
     */
    private function AddOneWeek($timestamp): string
    {
        return substr(\Carbon\Carbon::createFromTimestamp($timestamp)->addweek()->format(self::format), 0, 19) . '.000Z';
    }

    /**
     * videoとvideo_thumbnailをDBに保存する
     *
     * @param array $videos
     * @param array $video_thumbnails
     */
    private function saveVideosAndThumbnails(array $videos, array $video_thumbnails): void
    {
        for ($i = 0; $i < count($videos); $i++) {
            dump($videos[$i]); // あえて入れている
            $saved_video = $this->video_repo->saveRecord($videos[$i]);
            $video_thumbnails[$i]['video_id'] = $saved_video->id;
            $this->video_thumbnail_repo->saveRecord($video_thumbnails[$i]);
        }
    }
}
