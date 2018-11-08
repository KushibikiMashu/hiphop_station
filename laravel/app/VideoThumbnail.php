<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoThumbnail extends Model
{
    protected $table = 'video_thumbnail';
    public $timestamps = true;

    public function video()
    {
        return $this->belongsTo('App\Video');
    }
}
