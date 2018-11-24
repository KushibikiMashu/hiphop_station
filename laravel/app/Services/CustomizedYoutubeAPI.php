<?php

namespace App\Services;

class CustomizedYoutubeApi extends \Youtube {

    /**
     * List videos in the channel
     *
     * @param string $channelId
     * @param int $maxResults
     * @param null $publishedAfter
     * @param null $publishedBefore
     * @param null $order
     * @param array $part
     * @param bool $pageInfo
     * @return array
     * @throws \Exception
     */
    public function listChannelVideos($channelId, $maxResults = 10, $publishedAfter = null, $publishedBefore = null, $order = null, $part = ['id', 'snippet'], $pageInfo = false)
    {
        $params = [
            'type' => 'video',
            'channelId' => $channelId,
            'part' => implode(', ', $part),
            'maxResults' => $maxResults,
            'publishedAfter' => $publishedAfter,
            'publishedBefore' => $publishedBefore,
        ];
        if (!empty($order)) {
            $params['order'] = $order;
        }
        return parent::searchAdvanced($params, $pageInfo);
    }
}
