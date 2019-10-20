<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;


  
class ImageUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imageUpload()
    {
       // return view('imageUpload');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imagepost(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
  
        $imageName = time().'.'.$request->file->extension();  
   
        $request->file->move(public_path('images'), $imageName);
   
        return response()->json([
            'message' => 'Upload success',
            'file' => $imageName
        ]);
   
    }
}