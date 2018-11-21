<?php

namespace App\Services;

use App\Repositories\VideoRepository;
use App\Repositories\VideoThumbnailRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\ApiRepository;
use App\Repositories\DownloadJpgFileRepository;

class FetchLatestVideosFromYoutubeApiService
{
    private $video_repo;
    private $video_thumbnail_repo;
    private $channel_repo;
    private $api_repo;
    private $jpg_repo;

    /**
     * サムネイル画像の大きさ
     */
    const sizes = ['std', 'medium', 'high'];

    public function __construct
    (
        VideoRepository $video_repo,
        VideoThumbnailRepository $video_thumbnail_repo,
        ChannelRepository $channel_repo,
        ApiRepository $api_repo,
        DownloadJpgFileRepository $jpg_repo
    )
    {
        $this->video_repo = $video_repo;
        $this->video_thumbnail_repo = $video_thumbnail_repo;
        $this->channel_repo = $channel_repo;
        $this->api_repo = $api_repo;
        $this->jpg_repo = $jpg_repo;
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
                $this->video_repo->saveRecord($this->prepare_video_record($video));
                $this->video_thumbnail_repo->saveRecord($this->prepare_video_thumbnail_record($video));
            }
        }
    }

    /**
     * videoテーブルに格納するレコードを作成する
     *
     * @param object $video
     * @return array
     */
    private function prepare_video_record($video): array
    {
        $channel_id = $this->channel_repo->fetchChannelIdByHash($video->snippet->channelId);
        $title = $video->snippet->title;
        $genre = $this->api_repo->determine_video_genre($channel_id, $title);

        return [
            'channel_id'   => $channel_id,
            'title'        => $title,
            'hash'         => $video->id->videoId,
            'genre'        => $genre,
            'published_at' => $video->snippet->publishedAt
        ];
    }

    /**
     * video_thumbnailテーブルに格納するレコードを作成する
     *
     * @param object $video
     * @return array
     */
    private function prepare_video_thumbnail_record($video): array
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
            foreach (self::sizes as $size) {
                $this->fetchThumbnailInDatabase($record, $size);
            }
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
        $url = str_replace('_live', '', $record->{$size});
        if (!$hash = $this->video_repo->getHashFromVideoThumbnail($record)) return;
        $file_path = "image/{$table}/{$size}/{$hash}.jpg";
        if (file_exists(public_path($file_path))) return;

        $result = $this->jpg_repo->couldDownloadJpgFromUrl($url, $file_path);
        if ($result === false) {
            \Log::warning('Cannot download image file from: ' . $url);
            $this->video_repo->deleteByHash($hash);
            $this->video_thumbnail_repo->deleteById($record->id);
            // $hashを使って画像も消す
        }
    }
}
