<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Meeting;
use Carbon\Carbon;
use App\Models\User;

class MeetingController extends Controller
{

    public function __construct()
    {
        //this->middleware('name');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::all();
        foreach ($meetings as $meeting) {
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'list of all meetings ',
            'meeting' => $meetings
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $veri = array(
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        );

        $validador = Validator::make($request->all(), $veri);

        if ($validador->fails()) {
            return $validador->errors();
        } else {

            $title = $request->input('title');
            $description = $request->input('description');
            $time = $request->input('time');
            $user_id = $request->input('user_id');

            $meeting = new Meeting([
                'time' => Carbon::createFromFormat('YmdHie', $time),
                'title' => $title,
                'description' => $description
            ]);
            if ($meeting->save()) {
                $meeting->users()->attach($user_id);
                $meeting->view_meeting = [
                    'href' => 'api/v1/meeting/' . $meeting->id,
                    'method' => 'GET'
                ];
                $message = [
                    'msg' => 'Meeting created',
                    'meeting' => $meeting
                ];
                return response()->json($message, 201);
            };
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id', $id)->firstOrFail();
        $meeting->vie_meeting = [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Meeting information',
            'meeting' => $meeting
        ];

        return response()->json($response, 200);
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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = Meeting::with('users')->findOrFail($id);
     
        if($meeting->users()->where('users.id', $user_id)->first()){
           // $user = User::findOrFail($id)-get();
           return response()->json(['msg' => 'user not registered for meeting, update not successfull'], 401);
           //return response()->json([$meeting, 'msg' => 'no actualizado'], 200);
        };
      
        $meeting->time = Carbon::createFromFormat('Ymdhie', $time);
        $meeting->title = $title;
        $meeting->description = $description;
        if($meeting->update()){
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting'.$meeting->id,
                'method' => 'GET'
            ];
            $response = [
                'msg' => 'Meeting update',
                'meeting' => $meeting,

            ];
    
            return response()->json($response, 200);
        };

        return response()->json(['msg' => 'Error during UPDATING'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [
            'msg' => 'Meeting delete',
            'created' => [
                'href' => 'api/v1/meeting',
                'method' => 'GET',
                'params' => 'title, description, time'
            ]
        ];

        return response()->json($response, 200);
    }
}
