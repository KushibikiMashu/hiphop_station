<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Video;
use App\Repositories\VideoRepository;

class VideoRepositoryTest extends TestCase
{
    private $video_repository;

    public function setUp(): void
    {
        parent::setup();
        $this->video_repository = new VideoRepository;
    }

    /**
     * @test
     */
    public function fetchAll__Videoテーブルのレコードを全て取得する(): void
    {
        $expected = $actual = [];
        $generator = $this->video_repository->fetchAll();
        foreach (iterator_to_array($generator) as $record) {
            $actual[] = $record->getOriginal();
        }
        foreach (Video::get() as $record) {
            $expected[] = $record->getOriginal();
        }
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getTable__テーブル名を取得する(): void
    {
        $expected = $this->video_repository->getTableName();
        $this->assertSame('video', $expected);
    }

    /**
     * @test
     */
    public function getHashFromVideoThumbnail__hashでレコードを削除する(): void
    {
        $record = $this->createVideoRecord();
        $table = $record->getTable();
        $hash = $record->hash;
        $data = ['hash' => $hash];
        $this->assertDatabaseHas($table, $data);
        $this->video_repository->deleteByHash($hash);
        $this->assertDatabaseMissing($table, $data);
    }
}
