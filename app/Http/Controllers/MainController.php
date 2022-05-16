<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //
    public function index(){
       $date = \Carbon\Carbon::today()->subDays(20);
       $title = "الفيديوهات الأكثر مشاهدة خلال هذا الاسبوع";
       $videos = Video::join('views', 'videos.id', '=', 'views.video_id')
                        ->orderBy('views.views_number', 'DESC')
                        ->where('videos.created_at', '>=', $date)
                        ->take(16)
                        ->get('videos.*'); 
        return view('main', compact('videos', 'title'));
    }

    public function channelsVideos(User $channel){
        $videos = Video::where('user_id', $channel->id)->get();
        $title = 'جميع الفيديهات الخاصة بالقناة: '.$channel->name;
        return view('videos.my-videos', compact('videos', 'title')); 
    }
   
}
