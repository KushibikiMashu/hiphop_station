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
            $channel                   = $this->channel_repo->fetchChannelByChannelId($video['channel_id']);
            $video['channel']['title'] = $channel->title;
            $video['channel']['hash']  = $channel->hash;
            $video['thumbnail']        = [
                'std'    => '/image/video_thumbnail/' . config('const.SIZES')[0] . "/{$video['hash']}.jpg",
                'medium' => '/image/video_thumbnail/' . config('const.SIZES')[1] . "/{$video['hash']}.jpg",
                'high'   => '/image/video_thumbnail/' . config('const.SIZES')[2] . "/{$video['hash']}.jpg"
            ];
            $video['published_at']     = (new \Carbon\Carbon($video['published_at']))->format('Y-m-d H:i:s');
            $video['diff_date']        = $this->getDateDiff($video['published_at']);
            $new_query[]               = $video;
        }
        return $new_query;
    }

    private function getDateDiff(string $datetime): string
    {
        $now          = new \Carbon\Carbon();
        $published_at = new \Carbon\Carbon($datetime);

        $diff_seconds   = $now->diffInSeconds($published_at);
        if(0 <= $diff_seconds && $diff_seconds < 60) return $diff_seconds . '1分前';

        $diff_minutes   = $now->diffInMinutes($published_at);
        if(1 <= $diff_minutes && $diff_minutes < 60) return $diff_minutes . '分前';

        $diff_hours   = $now->diffInHours($published_at);
        if(1 < $diff_hours && $diff_hours < 24) return $diff_hours . '時間前';

        $diff_days   = $now->diffInDays($published_at);
        if(1 <= $diff_days && $diff_days < 7) return $diff_days . '日前';

        $diff_weeks   = $now->diffInWeeks($published_at);
        if(1 <= $diff_weeks && $diff_weeks < 4) return $diff_weeks . '週間前';

        $diff_months   = $now->diffInMonths($published_at);
        if(1 <= $diff_months && $diff_months < 12) return $diff_months . 'ヶ月前';

        $diff_years   = $now->diffInYears($published_at);
        if(1 <= $diff_years) return $diff_years . 'ヶ月前';

        return $published_at;
    }
}
