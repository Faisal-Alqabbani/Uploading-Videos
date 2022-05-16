<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //
    public function index(){
        $user = User::find(auth()->id());
        $videos = $user->videoInHistory()->get();
        $title = "سجل المشاهدات";
        return view('history.history-index', compact('videos', 'title'));
    }

    public function destroy($id){
        auth()->user()->videoInHistory()->wherePivot('id', $id)->detach();
        return back()->with('success', 'تم حذف المقطع من سجل المشاهدة بنجاح');
    }

    public function destroyAll(Request $request){
        auth()->user()->videoInHistory()->detach();
        return redirect()->back()->with('success', 'تم حذف جميع محتويات السجل');
    }
}
