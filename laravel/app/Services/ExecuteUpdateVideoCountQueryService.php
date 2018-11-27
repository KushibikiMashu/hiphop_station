<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 2018/11/24
 * Time: 16:38
 */

namespace App\Services;

use App\Repositories\ChannelRepository;
use App\Repositories\ApiRepository;

class ExecuteUpdateVideoCountQueryService extends BaseService
{
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
            $this->updateVideoCount($record);
        }
    }

    /**
     * 全てのchannelの動画数を更新する
     *
     * @param $channel
     * @throws \Exception
     */
    private function updateVideoCount($channel): void
    {
        $video_count = $this->api_repo->getVideoCountByChannelHash($channel->hash);
        $this->channel_repo->updateVideoCount($channel->id, $video_count);
    }
}
