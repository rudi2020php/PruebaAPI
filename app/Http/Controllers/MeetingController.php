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

            $usr = User::find($user_id);
            if($usr === null){
                return response()->json(['msg' => 'Usuario no registrado en la BDD', 'usuario'=>$usr], 200);
            };

            $meeting = new Meeting([
                'time' => Carbon::createFromFormat('YmdHie', $time),
                'title' => $title,
                'description' => $description
            ]);

            if ($meeting->save()) {
                //aquí debe de iniciar el if para verificar el usuario a insertar
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
            'href' => 'api/v1/meeting/'. $id,
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
       
            $usr =$meeting->users()->where('users.id', $user_id)->first();
            //return response()->json($usr);
        if(!$usr){
           $user = User::findOrFail($user_id);
          //$usuario = $meeting->users();
           return response()->json(['msg' => 'user not registered for meeting, update not successful',$user], 401);
           //return response()->json([$meeting, 'msg' => 'no actualizado'], 200);
        };
     
        $meeting->time = Carbon::createFromFormat('Ymdhie', $time);
        $meeting->title = $title;
        $meeting->description = $description;
        if($meeting->update()){
            //$meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
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
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users;
        $meeting->users()->detach();
        if(!$meeting ->delete()){
            foreach($users as $user){
                $meeting->users()->attach($user);
            }
            return response()->json(['msg' => 'delete failed'], 404);
        };
        $response = [
            'msg' => 'Meeting deleted',
            'created' => [
                'href' => 'api/v1/meeting',
                'method' => 'GET',
                'params' => 'title, description, time'
            ]
        ];

        return response()->json($response, 200);
    }
}
