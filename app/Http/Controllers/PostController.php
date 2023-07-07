<?php

namespace App\Http\Controllers;

// import Model "post"
use App\Models\Post;
use Illuminate\Cache\RedisTagSet;
// use Illuminate\View\View;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        // get posts
        $posts = Post::latest()->paginate(3);

        return view('posts.index', compact('posts'));


    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // validasi form
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());


        // create post
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content

        ]);

        //redirect ke index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);

    }

    public function show(string $id)
    {

        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.show', compact('post'));

    }

    public function edit(string $id)
    {

        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        // validate form
        $this->validate($request, [
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        //get post by ID
        $post = Post::findOrFail($id);
        
        //cek if/jika gambar di upload
        if ($request->hasFile('image')) {

            //upload gambar baru setelah di update
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            //hapus gambar lama
            Storage::delete('public/posts'.$post->image);

            //update post dengan gambar baru
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);


        } else {
            // update post tanpa gambar
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }

        //redirect ke index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil di Update!']);





    }

    public function destroy($id)
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //hapus gambar
        Storage::delete('public/posts'. $post->image);

        //hapus post
        $post->delete();


        //redirect to index
        return redirect()->route('posts.index')-> with(['success' => 'Data Berhasil di Hapus!']);


    }





}



