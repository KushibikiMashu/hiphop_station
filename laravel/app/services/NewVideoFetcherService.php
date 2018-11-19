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

    const sizes = ['std', 'medium', 'high'];
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
    public function run()
    {
        $new_channels = $this->getNewChannelHash();
//        $this->saveVideosAndThumbnails($new_channels);
        dd($new_channels);

        // loop文でlistChannelVideos()を適用する
        // １週間ずつ取得する

        // Videoに登録する
        // Video_Thumbnailに登録する
        // (fetch:allVideoThumbnailコマンドを実行する)

    }

    /**
     * 紐づくvideoがないchannelのhashを取得する
     *
     * @return array
     */
    private function getNewChannelHash(): array
    {
        $new_channels = [];
        foreach ($this->channel_repo->fetchAll() as $record) {
            if ($this->video_repo->channelVideoExists($record->id)) {
                continue;
            }
            $new_channels[] = [
                'id'   => $record->id,
                'hash' => $record->hash
            ];
        }
        return $new_channels;
    }


    private function saveVideosAndThumbnails(array $videos, array $video_thumbnails)
    {
        dump($videos[0]);
        dd($video_thumbnails[0]);
        for ($i = 0; $i < count($videos); $i++) {
            $saved_video = $this->video_repo->saveRecord($videos[$i]);
            $video_thumbnails['video_id'] = $saved_video['id'];
            $this->video_thumbnail_repo->saveRecord($video_thumbnails[$i]);
        }
    }

    private function BetweenPublishedAtToNow()
    {
        $now_time = strtotime('now');
        $pub_time = strtotime(new \DateTime($published_at));
        while ($pub_time < $now_time) {
            $start = $this->convertToYoutubeDatetimeFormat($pub_time);
            $end = $this->AddOneWeek($pub_time);

            // beforeが現在時刻を超えたら、現在時刻を利用する
            if ($now_time < strtotime($end)) {
                $end = $this->convertToYoutubeDatetimeFormat($now_time);
            }

            // listChannelVideoで取得
            // [$videos, $video_thumbnails] = $this->api_repo->getNewVideosByChannelHash($channel_id, $channel_hash, $maxResult, $start, $end);
            // if (is_null($video)) continue;

            // save関数でDBに保存
            // $this->saveVideosAndThumbnails($videos, $video_thumbnails);

            // 本来のインクリメントはこう書く
//            $pub_time += 86400 * 7;
        }
    }

    private function convertToYoutubeDatetimeFormat($timestamp): string
    {
        return  substr(\Carbon\Carbon::createFromTimestamp($timestamp)->format(self::format), 0, 19) . '.000Z';
    }

    private function AddOneWeek($datetime): string
    {
        $timestamp = strtotime($datetime);
        return  substr(\Carbon\Carbon::createFromTimestamp($timestamp)->addweek()->format(self::format), 0, 19) . '.000Z';
    }

}
