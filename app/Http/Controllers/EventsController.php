<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events;

class EventsController extends Controller
{

    public function index()
    {
        $events =  Events::all();
        $data = array();
        foreach($events as $event) {
            $tmp = array();
            $tmp['id'] = $event->id;
            $tmp['title'] = $event->event_name;
            $tmp['start'] = $event->event_date;
            $tmp['end'] = $event->event_date;
            array_push($data, $tmp);
        }
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // dd($request);
        foreach($request->events as $req) {
            $event = New Events();
            $event->event_name = $req['event_name'];
            $event->event_date = $req['event_date'];
            $event->save();
        }
    }
}
