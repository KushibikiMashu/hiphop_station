<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\ChannelRepository;

class CreateLatestJsonService
{
    private $video_repo;
    private $channel_repo;

    public function __construct(VideoRepository $video_repo, ChannelRepository $channel_repo)
    {
        $this->video_repo   = $video_repo;
        $this->channel_repo = $channel_repo;
    }

    public function getArrays(): array
    {
        $videos   = $this->video_repo->fetchColumnsOrderByPublishedAt('id', 'channel_id', 'title', 'hash', 'genre', 'published_at');
        $channels = $this->channel_repo->fetchAnyColumns('title', 'hash');
        $main     = $this->addExtraData($videos);
        return [$channels, $main];
    }

    /**
     * 動画に紐づくchannel情報とサムネイルのURLを追加する
     *
     * @param $videos
     * @return array
     */
    private function addExtraData($videos): array
    {
        $new_query = [];
        foreach ($videos as $video) {
            $video['title']            = mb_strimwidth($video['title'], 0, 50, '...');
            $video['published_at']     = (new \Carbon\Carbon($video['published_at']))->format('Y-m-d H:i:s');
            $video['diff_date']        = $this->getDateDiff($video['published_at']);
            $video['thumbnail']        = [
                'std'    => '/image/video_thumbnail/' . config('const.SIZES')[0] . "/{$video['hash']}.jpg",
                'medium' => '/image/video_thumbnail/' . config('const.SIZES')[1] . "/{$video['hash']}.jpg",
                'high'   => '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video['hash']}.jpg"
            ];
            $channel                   = $this->channel_repo->fetchChannelByChannelId($video['channel_id']);
            $video['channel']['title'] = $channel->title;
            $video['channel']['hash']  = $channel->hash;
            $new_query[]               = $video;
        }
        return $new_query;
    }

    /**
     * 現在時刻と動画公開日の差を取得する
     *
     * @param string $datetime
     * @return string
     */
    private function getDateDiff(string $datetime): string
    {
        $now    = new \Carbon\Carbon();
        $target = new \Carbon\Carbon($datetime);

        $diff_seconds = $now->diffInSeconds($target);
        if (0 <= $diff_seconds && $diff_seconds < 60) return '1分前';

        $diff_minutes = $now->diffInMinutes($target);
        if (1 <= $diff_minutes && $diff_minutes < 60) return $diff_minutes . '分前';

        $diff_hours = $now->diffInHours($target);
        if (1 <= $diff_hours && $diff_hours < 24) return $diff_hours . '時間前';

        $diff_days = $now->diffInDays($target);
        if (1 <= $diff_days && $diff_days < 7) return $diff_days . '日前';

        $diff_weeks = $now->diffInWeeks($target);
        if (1 <= $diff_weeks && $diff_weeks < 4) return $diff_weeks . '週間前';

        $diff_months = $now->diffInMonths($target);
        if (1 <= $diff_months && $diff_months < 12) return $diff_months . 'ヶ月前';

        $diff_years = $now->diffInYears($target);
        if (1 <= $diff_years) return $diff_years . '年前';

        return $target->format('Y-m-d H:i:s');
    }
}
