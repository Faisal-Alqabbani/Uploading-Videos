@extends('layouts.main')



@section('content')
<div class="mx-4">
    {{-- search bar section started here --}}
    <div class="row justify-content-center align-items-center">
        <form action="{{route('video.search')}}" class="d-flex col-md-6 justify-content-center align-items-center">
            <input type="text" class='form-control' name="term" />
            <button class="btn btn-outline-primary p-2" type='submit'>ابحث</button>
        </form>
    </div>
    <br>
    <hr>
    <br>
    {{-- end search section  --}}
    <p class="my-4">{{$title}}</p>
    <div class="row">
        @forelse($videos as $video)
            @if($video->processed)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card" style="width: 18rem;">
                            {{-- card-icons --}}
                            <div class="card-icons">
                                @php 
                                    $hours_add_zero = sprintf("%02d", $video->hours);
                                @endphp
                                @php 
                                    $minutes_add_zero = sprintf("%02d", $video->minutes);
                                @endphp
                                @php 
                                    $seconds_add_zero = sprintf("%02d", $video->seconds);
                                @endphp
                                <a href="/videos/{{$video->id}}">
                                    <img src="{{Storage::url($video->image_path)}}" class="card-img-top" alt="card-image">
                                    <time>{{$video->hours > 0 ? $hours_add_zero.':':''}} {{$minutes_add_zero}}:{{$seconds_add_zero}}</time>
                                    <i class="fas fa-play fa-2xl"></i>
                                </a>
                            </div>  
                            <div class="card-body p-0">
                              <a href="/videos/{{$video->id}}">
                              <p class="card-title">{{Str::limit($video->title, 60)}}</p>
                              </a>
                            </div>
                            {{-- card  footer --}}
                            <div class="card-footer">
                                <small class='text-muted'>
                                  @foreach($video->views as $view)
                                    <span class="d-block"><i class="fas fa-eye"></i>   مشاهدات {{$view->views_number}} </span> 
                                  @endforeach
                                 
                                  <i class="fas fa-clock"></i> <span>منذ 6 ساعات</span>  
                                  {{-- check if the user logged in and the user has the video or not --}}
                                  @auth 
                                    @if($video->user_id === auth()->user()->id)
                                    {{-- delete form --}}
                                        @if(!auth()->user()->block)
                                    <div class="d-flex justify-around">
                                        <form action="{{route('videos.destroy', $video->id)}}" method='POST' onsubmit="return confirm('are you sure you want to delete this video?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="float-right"><i class="fas fa-trash text-danger fa-lg"></i></button>
                                        </form>
                                        {{-- edit form --}}
                                        <form method="GET" action="{{route('videos.edit', $video->id)}}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="float-left"><i class="far fa-edit text-success fa-lg ml-3"></i></button>
                                        </form> 
                                    </div>
                                       @endif
                                    @endif
                                  @endauth
                                </small>
                            </div>
                        </div>
                    </div>
            @endif
        @empty
            <div class="mx-auto col-8">
                <div class='alert alert-secondary text-center' role='alert'>
                    لايوجد فيديوهات                      
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection