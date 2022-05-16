<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/597cb1f685.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="{!! asset('theme/css/sb-admin-2.css') !!}" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">


    <title>مقاطع الفيديو</title>
</head>
<body>
    <div dir="rtl" class="text-align:right;">
        <nav class="navbar navbar-expand-lg navbar-light bg-light ">
            <div class="container">
              <a class="navbar-brand" href="#">كورسات</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                      {{-- li items --}}
                      <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-home"></i>
                                الصفحة الرئيسية                                     
                            </a>
                      </li>

                      {{-- li items --}}
                      <li class="nav-item">
                        <a href="{{route('history')}}" class="nav-link">
                            <i class="fas fa-history"></i>
                            سجل المشاهدات                                   
                        </a>
                      </li>

                        {{-- li items --}}
                        <li class="nav-item">
                            <a href="{{route('videos.create')}}" class="nav-link">
                                <i class="fas fa-upload"></i>
                                رفع الفيديو                                  
                            </a>
                         </li>

                      {{-- li items --}}
                      <li class="nav-item">
                        <a href="{{route('videos.index')}}" class="nav-link">
                            <i class="far fa-play-circle"></i>
                            فديوهاتي                                  
                        </a>
                     </li>
                  {{-- li items --}}
                    <li class="nav-item">
                    <a href="{{route('channel.index')}}" class="nav-link">
                        <i class="fas fa-film"></i>
                        القنوات                               
                      </a>
                    </li>
                </ul>
                  {{-- end ul --}}
                {{-- start new ul --}}
                <ul class="navbar-nav mr-auto">
                    {{-- Alert started here  --}}
                    <div class="topbar" style="z-index:1">
                        @auth
                            <!-- Nav Item - Alerts -->
                            <li class="nav-item dropdown no-arrow alert-dropdown mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell fa-fw fa-lg"></i>
                                    <!-- Counter - Alerts -->
                                    <span class="badge badge-danger badge-counter notif-count" data-count="{{App\Models\Alert::where('user_id', Auth::user()->id)->first()->alert}}">{{App\Models\Alert::where('user_id', Auth::user()->id)->first()->alert}}</span>
                                </a>
                                <!-- Dropdown - Alerts -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right text-right mt-2"
                                    aria-labelledby="alertsDropdown">
                                    <div class="alert-body">
                                        
                                    </div>
                                    <a class="dropdown-item text-center small text-gray-500" href="{{route('all.Notification')}}">عرض جميع الإشعارات</a>
                                </div>
                            </li>
                        @endauth
                    </div>
                    @guest 
                        <li class="nav-item mt-2">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('تسجيل الدخول') }}</a>
                        </li>
                    @if (Route::has('register'))
                        <li class="nav-item mt-2">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('إنشاء حساب') }}</a>
                        </li>
                     @endif
                     @else
                     <li class="nav-item dropdown justify-content-left mt-2">
                        <a id="navbarDropdown" class="nav-link" href="#" data-toggle="dropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </a>

                          {{-- dropdown menu --}}
                         <div class="dropdown-menu dropdown-menu-left px-2 text-right mt-2" aria-labelledby="navbarDropdown">
                             @can('update-video')
                                <a href="{{route("admin.index")}}" class="dropdown-item text-right">لوحة التحكم</a>
                             @endcan
                        <!-- Responsive Settings Options -->
                                <div class="pt-4 pb-1 border-t border-gray-200">
                                    <div class="flex items-center px-4">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <div class="shrink-0 mr-3">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                            </div>
                                        @endif

                                        <div class="ml-3">
                                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-3 space-y-1">
                                        <!-- Account Management -->
                                        <x-jet-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                                            {{ __('Profile') }}
                                        </x-jet-responsive-nav-link>

                                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                            <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                                                {{ __('API Tokens') }}
                                            </x-jet-responsive-nav-link>
                                        @endif

                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}" x-data>
                                            @csrf
                                            <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                                        @click.prevent="$root.submit();">
                                                {{ __('Log Out') }}
                                            </x-jet-responsive-nav-link>
                                        </form>

                                        <!-- Team Management -->
                                        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                                            <div class="border-t border-gray-200"></div>

                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Manage Team') }}
                                            </div>

                                            <!-- Team Settings -->
                                            <x-jet-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                                                {{ __('Team Settings') }}
                                            </x-jet-responsive-nav-link>

                                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                                <x-jet-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                                                    {{ __('Create New Team') }}
                                                </x-jet-responsive-nav-link>
                                            @endcan

                                            <div class="border-t border-gray-200"></div>

                                            <!-- Team Switcher -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Switch Teams') }}
                                            </div>

                                            @foreach (Auth::user()->allTeams() as $team)
                                                <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                           </div>
                     </li>
                   
                    @endguest
                </ul>
              </div>
            </div>
          </nav>
          {{-- end navbar --}}
          {{-- main  --}}
          <main class="py-4">
                @if(Session::has('success'))
                    <div class="p-3 mb-2 bg-success text-white rounded mx-auto col-8">
                        <span class="text-center">{{ session('success') }}</span>
                    </div>  
                @endif
                {{-- content section --}}
                <div class="container">
                    @yield('content')
                </div>
                    
          </main>
    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
    
        var pusher = new Pusher('058d63dd8dca286e7496', {
          cluster: 'mt1'
        });
    </script>
    
    <script src="{{asset('js/pushNotification.js')}}"></script>
    <script src="{{asset('js/failedNotifications.js')}}"></script>
          @yield('script')
    <script>
        var token = "{{ Session::token()}}";
        var urlNotify = "{{route('notification')}}";
        $('#alertsDropdown').on('click', function(event){ 
           event.preventDefault();
           var notificationsWrapper = $('.alert-dropdown');
           var notificationsToggle = notificationsWrapper.find('a[data-toggle]');
           var notificationsCountElem = notificationsToggle.find('span[data-count]');
           notificationsCount = 0;
           notificationsCountElem.attr("data-count", notificationsCount);
           notificationsWrapper.find(".notif-count").text(notificationsCount);
           notificationsWrapper.show();

            $.ajax({
                method: 'POST',
                url:urlNotify,
                data:{
                    _token: token
                },
                success: function(data){
                    var responseNotifications = "";
                    $.each(data.someNotifications, function(i, item){
                        var responseDate = new Date(item.created_at);
                        var date = responseDate.getFullYear()+"-"+(responseDate.getMonth() + 1)+"-"+responseDate.getDate(); 
                        var time = responseDate.getHours()+":"+responseDate.getMinutes()+ ":"+responseDate.getSeconds();

                        if(item.success){
                            responseNotifications +=   '<a class="dropdown-item d-flex align-items-center" href="#">\
                                                        <div class="ml-3">\
                                                            <div class="icon-circle bg-secondary">\
                                                                <i class="far fa-bell text-white"></i>\
                                                            </div>\
                                                        </div>\
                                                        <div>\
                                                            <div class="small text-gray-500">'+date+' الساعة '+time+'</div>\
                                                            <span>تهانينا لقد تم معالجة مقطع الفيديو <b>'+item.notification+'</b> بنجاح</span>\
                                                        </div>\
                                                    </a>';
                        }else{
                            responseNotifications += '<a class="dropdown-item d-flex align-items-center" href="#">\
                                    <div class="ml-3">\
                                        <div class="icon-circle bg-secondary">\
                                            <i class="far fa-bell text-white"></i>\
                                        </div>\
                                    </div>\
                                                        <div>\
                                                            <div class="small text-gray-500">' +
                            date + "الساعة " + time + "</div>\
                                                            <span>للأسف حدث خطأ غير متوقع أثناء معالجة مقطع الفيديو <b>" +
                            data.video_title +
                            "</b> يرجى رفعه مرة أخرى</span>\
                                                        </div>\
                                </a>";
                        }
                    $('.alert-body').html(responseNotifications);
                    })
                }
            })

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>