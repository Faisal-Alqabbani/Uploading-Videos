<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertVideoStreaming;
use App\Models\Convertedvideo;
use App\Models\Like;
use App\Models\Video;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
class VideoController extends Controller
{

    public function __construct(){
        $this->middleware('auth')->except(['show','addView']);        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $videos = auth()->user()->videos->sortByDesc('created_at');
        $title = 'اخر الفدوهات المرفوعة'; 
        return view('videos.my-videos', compact('videos', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('videos.uploader'); 
    } 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation part
        $this->validate($request, [
            'title' => 'required',
            'image' => 'image|required',
        ]);
        $randomPath = Str::random(16);
        $videoPath = $randomPath.'.'.$request->video->getClientOriginalExtension();
        $imagePath = $randomPath.'.'.$request->image->getClientOriginalExtension();
        $image = Image::make($request->image)->resize(320, 180);
        
        $path = Storage::put($imagePath, $image->stream()); 
        $request->video->storeAs('/', $videoPath, 'public');

        $video = Video::create([
            'disk' => 'public',
            'video_path' => $videoPath,
            'image_path' => $imagePath,
            'title' => $request->title,
            'user_id' => auth()->id(),
        ]);

        $view = View::create([
            'video_id' => $video->id,
            'user_id' => auth()->id(),
            'views_Number' => 0, 
        ]);
        // job to procecing in the background!
        ConvertVideoStreaming::dispatch($video);
        return redirect()->back()->with('success', 'سيكون مقطع الفيديو موجود في اقصر وقت بعد المعالجة');
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //print show information 
        $countLike =  Like::where('video_id', $video->id)->where('like', '1')->count(); 
        $countDislike = Like::where('video_id', $video->id)->where('like', '0')->count();
        $user = Auth()->user(); 
        if(Auth()->check()){
            $userLike = $user->likes()->where('video_id', $video->id)->first();
        }else{
            $userLike = 0;
        }
        // check if the user logged in add the video in his history
        if(Auth()->check()){
            auth()->user()->videoInHistory()->attach($video->id);
        }
        $comments = $video->comments->sortByDesc('created_at');
        return view('videos.show-video', compact(['video', 'userLike','countLike', 'countDislike', 'comments']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $video = Video::where('id', $id)->first();
        return view('videos.edit-video', compact('video'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'title' => 'required'
        ]);
        $video = Video::where('id', $id)->first();
        if($request->has('image')){
            $randomPath = Str::random(16);
            $newPath = $randomPath.'.'.$request->image->getClientOriginalExtension();
            Storage::delete($video->image_path);
            $image = Image::make($request->image)->resize(320, 180);
            // Store with stream to save the image in the cloud
            Storage::put($newPath, $image->stream());
            $video->image_path = $newPath;
        }
        $video->title = $request->title;
        $video->save();
        return redirect('/videos')->with('success', 'تم تعديل معلومات الفيديو بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $video = Video::where('id', $id)->first();
        $convertedVideos = Convertedvideo::where('video_id', $id)->get();
        foreach ($convertedVideos as $covertedVideo) { 
            Storage::delete([
                $covertedVideo->mp4_Format_240,
                $covertedVideo->mp4_Format_360,
                $covertedVideo->mp4_Format_480,
                $covertedVideo->mp4_Format_720,
                $covertedVideo->mp4_Format_1080,
                $covertedVideo->webm_Format_240,
                $covertedVideo->webm_Format_360,
                $covertedVideo->webm_Format_480,
                $covertedVideo->webm_Format_720,
                $covertedVideo->webm_Format_1080,
                $video->image_path
            ]);
        }
        $video->delete();
        return back()->with('success', 'تم حذف مقطع الفيديو بنجاح');
    }


    public function search(Request $request){
        $videos = Video::where('title', 'like',"%{$request->term}%")->paginate(12);
        $title = ' عرض نتائج البحث '.$request->term;
        return view('videos.my-videos', compact('videos', 'title'));
    }

    public function addView(Request $request){
        $views = View::where('video_id', $request->videoId)->first();
        $views->views_number++;
        $views->save();
        $views_number = $views->views_number;
        return response()->json(['viewsNumber' => $views_number]);

    }

    public function mostViewedVideos(){
        $mostViewedVideos = View::orderBy('views_number','Desc')
        ->take(10)
        ->get(['user_id', 'video_id', 'views_number']);
        $videoNames = [];
        $videoViews = [];
        foreach ($mostViewedVideos as $view) {
            array_push($videoNames, Video::find($view->video_id)->title);
            array_push($videoViews, $view->views_number);
        }
    
        return view('admin.most-Viewed-Videos', compact('mostViewedVideos'))->with('videoNames',json_encode($videoNames,JSON_NUMERIC_CHECK))->with('videoViews',json_encode($videoViews,JSON_NUMERIC_CHECK));
    }
}

