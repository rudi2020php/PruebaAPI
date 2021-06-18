<?php

namespace App\Http\Controllers;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;

class RegitrationController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'meeting_id'=> 'required',
            'user_id'=> 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User is already registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregistered' => [
                'href' => 'api/v1/regitration/'. $meeting_id,
                'method' => 'DELETE'
            ]
        ];
        if($meeting->users()->where('users.id', $user_id)->first()){
            return response()->json($message, 404);
        };
        $user->meetings()->attach($meeting);
        $response = [
            'msg' => 'User registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregistered' => [
                'href' => 'api/v1/regitration/'. $meeting_id,
                'method' => 'DELETE'
            ]
        ];

        return response()->json($response, 201);
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
        $meeting->users()->detach();

        $response =[
            'msg' => 'User unregistered for meeting',
            'meeting' => $meeting,
            'user' => 'tbdd',
            'registered' =>[
                'href' => 'api/v1/meeting/regitration',
                'method' => 'POST',
                'params' => 'user_id, meeting_id'
            ]
        ];

        return response()->json($response, 200);
    }
}
