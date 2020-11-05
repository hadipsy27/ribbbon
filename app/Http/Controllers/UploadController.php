<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Upload;
use App\Project;
use App\User;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr = $request()->all();
        $file = request()->file('file');
        $fileUrl = $file->storeAs("uploads/files/");
        $attr['user_id'] = request('user');

        $attr['user_id'] = request('user');
        $attr['file']   = $fileUrl;

        // $upload = auth()->user()->  


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function fileUpload(Request $request)
    {
        $this->validate($request,[
            'image' => 'required|mimes:jpeg,png,jpg,svg,zip|max:2048',

        ]);
        // $image = $request->file('image');
        // $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        // $destinationPath = public_path('/images');
        // $image->move($destinationPath, $input['imagename']);

        // $this->postImage->add($input);

        // return back()->with('Success', 'Upload success');

        // dd($request->all());
        $imageName = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'),$imageName);
        return back()->with('success', 'Upload successfully')
                     ->with('path',$imageName);
        
    }
}
