<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelThumbnail extends Model
{
    protected $table = 'channel_thumbnail';
    public $timestamps = true;

    public function channel()
    {
        return $this->belongsTo('App\Channel');
    }
}
