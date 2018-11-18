<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'video';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function video_thumbnail()
    {
        return $this->hasMany('App\VideoThumbnail');
    }
}
