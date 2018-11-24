<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelThumbnail extends Model
{
    protected $table = 'channel_thumbnail';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function channel()
    {
        return $this->belongsTo('\App\Channel');
    }
}
