<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\VideoApiService;

class ApiController extends Controller
{
    /**
     * published_atが１週間以内のデータを取得する
     * jsonの形式はreactが求めるもの
     *
     * @param VideoApiService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllVideos(VideoApiService $service): \Illuminate\Http\JsonResponse
    {
        return response()->json($service->getAllVideos());
    }
}
