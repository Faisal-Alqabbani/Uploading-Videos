<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    //
    public function index(){
        $channels = User::all()->sortByDesc('created_at');
        $title = 'احدث القنوات';
        return view('channels', compact('title', 'channels'));
    }
    // update channnel fron admin page
    public function adminUpdate(Request $request, User $user ){
        $user->administration_level = $request->administration_level;
        $user->save();
        session()->flash('flash_message', 'تم تعديل صلاحيات القناة بنجاح');
        return redirect(route('channels.index'));
    }

    public function adminDestroy(User $user){
        $user->delete();
        session()->flash('flash_message', 'تم حذف القناة بنجاح');
        return redirect(route('channels.index'));
    }

    public function adminBlock(Request $request, User $user){
        $user->block = 1;
        $user->save();
        session()->flash('flash_message', 'تم حظـر القناة بنجاح');
        return redirect(route('channels.index'));
    }

    public function blockedChannels(){
        $channels = User::where('block', 1)->get();
        return view('admin.channels.blocked-channels', compact('channels'));
    }

    public function openBlock(Request $request, User $user){
        $user->block = 0;
        $user->save();
        session()->flash('flash_message', 'تم فك حضر القناة بنجاح');
        return redirect(route('channels.blocked'));
    }

    public function allChannels(){
        $channels = User::all()->sortByDesc('created_at');
        return view('admin.channels.all', compact('channels'));
    }
}
