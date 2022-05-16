<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function convertedvideos(){
        return $this->hasMany(ConvertedVideo::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->hasMany(Likes::class);
    }

    public function views(){
        return $this->hasMany(View::class);
    }

    public function users(){
        return $this->belongsToMany(Vidoe::class, 'video_user', 'video_id', 'user_id')->withTimestamps()->withPivot('id');
    }
}
