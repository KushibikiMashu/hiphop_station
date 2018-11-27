<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewVideoService;

class ApiController extends Controller
{
    /**
     * published_atが１週間以内のデータを取得する
     * jsonの形式はreactが求めるもの
     *
     * @param NewVideoService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNew(NewVideoService $service): \Illuminate\Http\JsonResponse
    {
        return response()->json($service->getNewVideo());
    }
}
