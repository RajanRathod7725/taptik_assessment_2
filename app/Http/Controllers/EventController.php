<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return Event::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $add_data = array(
            'user_id'=>Auth::id(),
            'title'=>$request->title,
            'description'=>$request->description,
            'type'=>$request->type,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
        );
        return Event::create($add_data);
    }

    public function update(Request $request, Event $event)
    {
        $update_data = array(
            'title'=>$request->title,
            'description'=>$request->description,
            'type'=>$request->type,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
        );
        $event = Event::find($event->id)->update($update_data);
        return $event;
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->noContent();
    }
}
