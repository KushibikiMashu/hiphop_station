<?php

namespace App\Services;

class FetchLatestVideosFromYoutubeApiService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(): array
    {
        $responses = $this->api_repo->getNewVideosOfRegisteredChannel();
        // 新着動画がない場合は処理を終える
        if (empty($responses)) {
            return $responses;
        }

        $this->saveVideosAndThumbnails($responses);
        $this->downloadVideoThumbnails(count(array_collapse($responses)));
        return $responses;
    }

    /**
     * 新着動画とサムネイルのデータをvideo, video_thumbnailテーブルに登録する
     *
     * @param array $responses
     */
    private function saveVideosAndThumbnails(array $responses): void
    {
        $registered_video_hashes = $this->video_repo->fetchPluckedColumn('hash')->flip();
        foreach ($responses as $videos) {
            foreach ($videos as $video) {
                // videoのhashが重複していればskipする
                if (isset($registered_video_hashes[$video->id->videoId])) {
                    continue;
                }
                $this->video_repo->saveRecord($this->prepareVideoRecord($video));
                $this->video_thumbnail_repo->saveRecord($this->prepareVideoThumbnailRecord($video));
            }
        }
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param object $video
     * @return array
     */
    private function prepareVideoRecord($video): array
    {
        $channel = $this->channel_repo->getChannelByChannelHash($video->snippet->channelId);
        $channel_id = $channel->id;
        $title      = $video->snippet->title;
        $genre      = $this->api_repo->getGenre($channel_id, $channel->hash, $title);
        \Log::info($title);

        return [
            'channel_id'   => $channel_id,
            'title'        => $title,
            'hash'         => $video->id->videoId,
            'genre'        => $genre,
            'published_at' => (new \Carbon\Carbon($video->snippet->publishedAt))->format('Y-m-d H:i:s')
        ];
    }

    /**
     * video_thumbnailテーブルに格納するレコードを作成する
     *
     * @param object $video
     * @return array
     */
    private function prepareVideoThumbnailRecord($video): array
    {
        return [
            'video_id' => $this->video_repo->fetchVideoIdByHash($video->id->videoId),
            'std'      => str_replace('_live', '', $video->snippet->thumbnails->default->url),
            'medium'   => str_replace('_live', '', $video->snippet->thumbnails->medium->url),
            'high'     => str_replace('_live', '', $video->snippet->thumbnails->high->url),
        ];
    }

    /**
     * video_thumbnailテーブルに格納されているアドレスの画像をダウンロードする
     *
     * @param int $sum
     */
    private function downloadVideoThumbnails(int $sum): void
    {
        $new_video_thumbnails = array_slice($this->video_thumbnail_repo->fetchAllOrderByAsArray('id'), 0, $sum);
        foreach ($new_video_thumbnails as $record) {
            if (\App\Video::find($record->video_id)->genre === 'not HIPHOP') continue;
            $this->fetchThumbnailInDatabase($record, config('const.SIZES')[2]);
        }
    }

    /**
     * file_get_contentsで画像を取得する
     *
     * @param object $record
     * @param string $size
     */
    private function fetchThumbnailInDatabase($record, string $size): void
    {
        $table = $this->video_thumbnail_repo->getTableName();
        $url   = str_replace('_live', '', $record->{$size});
        if (!$hash = $this->video_repo->getHashFromVideoThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;

        $result = $this->jpg_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            \Log::warning('Cannot download image file from: ' . $url);
            $this->video_repo->deleteByHash($hash);
            $this->video_thumbnail_repo->deleteById($record->id);
        }
    }
}
