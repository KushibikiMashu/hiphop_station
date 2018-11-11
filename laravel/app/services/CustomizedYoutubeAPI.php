<?php

namespace App\Services;

use Alaouy\Youtube\Facades\Youtube;

class CustomizedYoutubeApi extends Youtube {

    /**
     * List videos in the channel
     *
     * @param string $channelId
     * @param integer $maxResults
     * @param string $publishedAfter
     * @param string $publishedBefore
     * @param string $order
     * @param array $part
     * @param bool $pageInfo
     * @return array
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
