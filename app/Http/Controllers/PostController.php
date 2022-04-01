<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function oneMany($id){
        $data= Post::all()->where("user_id", $id);
        return response()->json($data);
    }
    public function posts()
    {
        $posts =  Post::query()->where('user_id', '=', auth::user()->id)->get();
        return response()->json($posts);
    }

    public function index()
    {
        $posts = Post::get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $upload_post = public_path('storage');
        $new_name = time() . '.' . $request->image->getClientOriginalExtension();
        $request->image->move($upload_post, $new_name);

        $post = Post::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$new_name,
            'user_id'=>auth()->user()->id
        ]);
        $post->save();
        return response()->json($request->all());
    }


    public function show($id)
    {
        $post = Post::find($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {

        $post = Post::find($id);
        $post->title=$request->title;
        $post->description=$request->description;

        if($request->hasFile('image')){
            $destination = 'storage'.$post->image;
            if(File::exists($destination))
            {
                File::delete($destination);
            };
            $file = $request->file('image');
            $extension =$file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('storage', $filename);
            $post->image = $filename;
        }

        $post->update();

        return response()->json($post);
    }


    public function destroy($id)
    {
        $post = Post::find($id);
        $currentPhoto = $post->image;
        $userPhoto = public_path('storage').$currentPhoto;
        if(file_exists($userPhoto)) {
            @unlink($userPhoto);
        }
        $post->delete();
        return response()->json('Post deleted!');
    }
}
