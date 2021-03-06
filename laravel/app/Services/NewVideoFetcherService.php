<?php

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ApiRepository;

class NewVideoFetcherService extends BaseService
{
    const format = \DateTime::ATOM;

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * commandから呼び出す
     */
    public function run(): void
    {
        foreach ($this->channel_repo->fetchAll() as $record) {
            if($this->isSameVideoCount($record)) continue;
            $this->getNewVideosByChannel($record);
        }
    }

    /**
     * channelのvideo_countとDBの動画数を比較する
     *
     * @param $channel
     * @return bool
     */
    private function isSameVideoCount($channel): bool
    {
        $video_count = $this->channel_repo->fetchChannelByChannelId($channel->id)->video_count;
        $registered_video_sum = $this->video_repo->countVideoByChannelId($channel->id);
        if($video_count <= $registered_video_sum) return true;
        return false;
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

        // チャンネル公開日から3ヶ月ごとに取得する
        $now_time = strtotime('now');
        $pub_time = strtotime($channel->published_at);
        while ($pub_time < $now_time) {
            $start = $this->convertToYoutubeDatetimeFormat($pub_time);
            $end = $this->AddNityDays($pub_time);
            // startが現在時刻を超えたら、現在時刻を利用する
            if ($now_time < strtotime($end)) {
                $end = $this->convertToYoutubeDatetimeFormat($now_time);
            }
            dump($end); // あえて入れている
            [$videos, $video_thumbnails] = $this->api_repo->getNewVideosByChannelHash($channel->id, $channel->hash, 50, $start, $end);
            $pub_time += 86400 * 90;
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
    private function AddNityDays($timestamp): string
    {
        return substr(\Carbon\Carbon::createFromTimestamp($timestamp)->addDays(90)->format(self::format), 0, 19) . '.000Z';
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
