<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'channel';
    public $timestamps = true;

    public function channel_thumbnail()
    {
        return $this->hasMany('App\ChannelThumbnail');
    }
}
