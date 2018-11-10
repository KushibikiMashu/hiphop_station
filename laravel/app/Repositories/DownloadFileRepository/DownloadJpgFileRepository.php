<?php

namespace App\Repositories;


class DownloadJpgFileRepository implements DownloadFileRepositoryInterface
{
    // 別のRepository作って、そこに実装してもいいと思う。OnlineImageRepositoryとか。外部からのデータ取得なので、オニオン型の一番外だから
    public function canDownloadJpgFromUrl(string $url, string $image_path): bool
    {
        // レスポンスコードが400系・500系でもwarningを出さない
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        if (strpos($http_response_header[0], '200') !== false) {
            file_put_contents(public_path($image_path), $data);
            return true;
        }
        return false;
    }
}
