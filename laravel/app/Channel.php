<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'channel';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function video()
    {
        return $this->hasOne('\App\Video');
    }

    public function channel_thumbnail()
    {
        return $this->hasOne('\App\ChannelThumbnail');
    }
}
