<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class MeetingController extends Controller
{

    public function __construct(){
        //this->middleware('name');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'user_id' => 'user_id',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method'=> 'GET'
            ]
        ];

        $response = [
            'msg' => 'list of all meetings ',
            'meeting' => [
                $meeting, $meeting
            ]
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

        if($validador->fails()){
            return $validador->errors();
        }else{
       
        $title = $request->input('title');
        $description = $request->input('description');
        $time= $request->input('time');
        $user_id= $request->input('user_id');
        
        $meeting = [
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'user_id' => $user_id,
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method'=> 'GET'
            ]
        ];

        $response = [
            'msg' => 'Meeting created',
            'meeting' => $meeting
        ];

        return response()->json($response, 201);
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
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'user_id' => 'user Id',
            'view_meeting' => [
                'href' => 'api/v1/meeting',
                'method' => 'GET'
            ]
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
            'title'=> 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('Description');
        $time= $request->input('time');
        $user_id= $request->input('user_id');

        $meeting = [
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'user_id' => $user_id,
            'view_meeting' => [
                'href'=> 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $response =[
            'msg' => 'Meeting update',
            'meeting' => $meeting
        ];

        return response()->json($response, 200);
       
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
