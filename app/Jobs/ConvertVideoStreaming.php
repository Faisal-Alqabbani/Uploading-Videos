<?php

namespace App\Jobs;

use App\Events\FailedNotification;
use App\Events\RealNotification;
use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg;
use FFMpeg\Format\Video\WebM;
use Storage;
use App\Models\Convertedvideo;
use App\Models\Notification;
use FFMpeg\Filters\Video\VideoFilters;
use App\Models\Video;
class ConvertVideoStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $video;
    public $videoWidth;
    public $videoHeight;
    public $format;
    public $names;
    public $i;
    public function __construct(Video $video)
    {
        //
        $this->video = $video;
    }


    protected function convertVideo($loopNumber){
        // video format
        $this->format = array(
            array(
                (new X264("libmp3lame", 'libx264'))->setKiloBitrate(4096),
                (new WebM('libvorbis','libvpx'))->setKiloBitrate(4096),    
            ),
            array(
                (new X264("libmp3lame", 'libx264'))->setKiloBitrate(2048),
                (new WebM('libvorbis','libvpx'))->setKiloBitrate(2048),    
            ),
            array(
                (new X264("libmp3lame", 'libx264'))->setKiloBitrate(750),
                (new WebM('libvorbis','libvpx'))->setKiloBitrate(750),    
            ),
            array(
                (new X264("libmp3lame", 'libx264'))->setKiloBitrate(700),
                (new WebM('libvorbis','libvpx'))->setKiloBitrate(700),    
            ),
            array(
                (new X264("libmp3lame", 'libx264'))->setKiloBitrate(300),
                (new WebM('libvorbis','libvpx'))->setKiloBitrate(300),    
            ),
        );
        // video width 
        $this->videoWidth = array(1920,1280,854, 640, 426);
        // video height
        $this->videoHeight = array(1080,720,480,360,240);
        // It will get the file's name
        $this->names = array(
            array(
                '1080p-'.$this->getFileName($this->video->video_path,'.mp4'),
                '1080p-'.$this->getFileName($this->video->video_path,'.webm')
            ),
            array(
                '720p-'.$this->getFileName($this->video->video_path,'.mp4'),
                '720p-'.$this->getFileName($this->video->video_path,'.webm')
            ),
            array(
                '480p-'.$this->getFileName($this->video->video_path,'.mp4'),
                '480p-'.$this->getFileName($this->video->video_path,'.webm')
            ), 
            array(
                '360p-'.$this->getFileName($this->video->video_path,'.mp4'),
                '360p-'.$this->getFileName($this->video->video_path,'.webm')
            ),
            array(
                '240p-'.$this->getFileName($this->video->video_path,'.mp4'),
                '240p-'.$this->getFileName($this->video->video_path,'.webm')
            ),
        );

        // loop through this file
        for($this->i = $loopNumber; $this->i < 5; $this->i++){
            for($j=0;$j < 2;$j++){
                FFMpeg::fromDisk($this->video->disk)
                        ->open($this->video->video_path)
                        ->export()
                        ->toDisk(env('FILESYSTEM_DRIVER'))
                        ->inFormat($this->format[$this->i][$j])
                        ->addFilter(function(VideoFilters $filters){
                           $filters->resize(new Dimension($this->videoWidth[$this->i], $this->videoHeight[$this->i])); 
                        })
                        ->save($this->names[$this->i][$j]);
            }
        }

    }
    private function getFileName($filename, $type){
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename).$type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ffprobe = FFMpeg\FFProbe::create();
        $video1 = $ffprobe
            ->streams(public_path('/storage//'.$this->video->video_path)) // extracts streams informations
            ->videos()                      // filters video streams
            ->first();                       // returns the first video stream
        $width = $video1->get('width');
        $height = $video1->get('height');
        // get video duration
        $media = FFMpeg::fromDisk($this->video->disk)
                ->open($this->video->video_path);
        $durationInSeconds = $media->getDurationInSeconds();
        $hours = floor($durationInSeconds / 3600);
        $minutes = floor(($durationInSeconds / 60) % 60);
        $seconds = $durationInSeconds % 60;
        $quality = 0;
        // المقطع عرض
        if($width > $height){
            if(($width >= 1920) && ($height >= 1080)){
                $quality = 1080;
                $this->convertVideo(0);
            }elseif(($width >= 1280) && ($height >= 720) && ($width < 1920 && $height < 1080)){
                $quality = 720;
                $this->convertVideo(1);
            }elseif(($width >= 854) && ($height >= 480) && ($width < 1280 && $height < 720)){
                $quality = 480;
                $this->convertVideo(2);
            }elseif(($width >= 640) && ($height >= 360) && ($width < 854 && $height < 480)){
                $quality = 360;
                $this->convertVideo(3);
            }else{
                $quality = 240;
                $this->convertVideo(4);
            }
            // المقطع طولي 
        }elseif($height > $width){
            $this->video->update([
                'Longitudinal' => true
            ]);
            if(($height >= 1920) && ($width >= 1080)){
                $quality = 1080;
                $this->convertVideo(0);
            }elseif(($height >= 1280) && ($width>= 720) && ($height < 1920 && $width < 1080)){
                $quality = 720;
                $this->convertVideo(1);
            }elseif(($height >= 854) && ($width >= 480) && ($height < 1280 && $width < 720)){
                $quality = 480;
                $this->convertVideo(2);
            }elseif(($height >= 640) && ($width >= 360) && ($height < 854 && $width < 480)){
                $quality = 360;
                $this->convertVideo(3);
            }else{
                $quality = 240;
                $this->convertVideo(4);
            }
            
        }
        Storage::disk('public')->delete($this->video->video_path);
        $converted_video = new Convertedvideo; 
        for($i = 0;$i < 5;$i++){
            $converted_video->{'mp4_Format_'.$this->videoHeight[$i]} = $this->names[$i][0];
            $converted_video->{'webm_Format_'.$this->videoHeight[$i]} = $this->names[$i][1];
        }
        $converted_video->video_id = $this->video->id; 
        $converted_video->save();
        // save notification in database 
        $notification = new Notification(); 
        $notification->user_id = $this->video->user_id;
        $notification->notification = $this->video->title;
        $notification->save();

        $data = [
            'video_title' => $this->video->title,
        ]; 
        event(new RealNotification($data));

        $alert = Alert::where("user_id", $this->video->user_id)->first();
        $alert->alert++; 
        $alert->save();
        $this->video->update([
            'processed' => true,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'quality' => $quality,
        ]);
    }

    public function failed(){
        $notification = new Notification();
        $notification->user_id = $this->video->user_id;
        $notification->notification = $this->video->title;
        $notification->success = false;
        $notification->save();
        $data = [
            'video_title' => $this->video->title, 
        ];
        event(new FailedNotification($data));
        $alert = Alert::where("user_id", $this->video->user_id)->first();
        $alert->alert++; 
        $alert->save(); 
    }
  
}


// first handle code
// as reminder
 //
        // // 240p
        // $lowBitrateFormat = (new X264("libmp3lame", 'libx264'))->setKiloBitrate(500);
        // // 360
        // $low2_BitrateFormat = (new X264("libmp3lame", 'libx264'))->setKiloBitrate(900);
        // // 480p
        // $mediumBitrateFormat = (new X264("libmp3lame", 'libx264'))->setKiloBitrate(1500);
        // // 720p
        // $highBitrateFormat = (new X264("libmp3lame", 'libx264'))->setKiloBitrate(3000);
        // // ever quality will has its name
        // $convertedName = '240-'.$this->video->video_path;
        // $convertedName_360 = '360-'.$this->video->video_path;
        // $convertedName_480 = '480-'.$this->video->video_path;
        // $convertedName_720 = '720-'.$this->video->video_path;
        
        // FFMpeg::fromDisk($this->video->disk)
        // ->open($this->video->video_path)
        // ->addFilter(function($filters){
        //     $filters->resize(new Dimension(426,240));
        // })
        // ->export()
        // ->toDisk('public')
        // ->inFormat($lowBitrateFormat)
        // ->save($convertedName)

        // ->addFilter(function($filters){
        //     $filters->resize(new Dimension(640,360));
        // })
        // ->export()
        // ->toDisk('public')
        // ->inFormat($low2_BitrateFormat)
        // ->save($convertedName_360 )

        // ->addFilter(function($filters){
        //     $filters->resize(new Dimension(854,480));
        // })
        // ->export()
        // ->toDisk('public')
        // ->inFormat($mediumBitrateFormat)
        // ->save($convertedName_480)
        
        // ->addFilter(function($filters){
        //     $filters->resize(new Dimension(1280,720));
        // })
        // ->export()
        // ->toDisk('public')
        // ->inFormat($highBitrateFormat)
        // ->save($convertedName_720);
