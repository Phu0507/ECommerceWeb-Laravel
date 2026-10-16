<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $post;

    public function __construct(PostService $post)
    {
        $this->post = $post;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');


        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('publish');
        $search = $request->input('search');

        $postCatalogues = $this->post->getAllPage($perPage, $publish, $search);
        return view('aadmin.post.post.post_index', compact('config', 'postCatalogues'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryList = $this->post->getCategoryOptions();
        // Logic to show the form for creating a new user
        return view('aadmin.post.post.post_create', compact('config', 'categoryList'));
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        try {
            
            $this->post->create($data);
            return redirect()->route('post.index')
                ->with('success', 'Post catalogue created successfully.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Error creating post catalogue: ' . $errorMessage);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post catalogue: ' . $errorMessage]);
        }
    }

    public function edit($id)
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryOptions = $this->post->getById($id);
        $categoryList = $this->post->getCategoryOptions();
        
        if (!$categoryOptions) {
            return redirect()->route('post.index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.post.post.post_update', compact('id', 'config', 'categoryOptions', 'categoryList'));
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->post->update($id, $data);

            return redirect()->route('post.index')
                ->with('success', 'Post catalogue updated successfully.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Error updating post catalogue: ' . $errorMessage);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update post catalogue: ' . $errorMessage]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->post->delete($id);
            return redirect()->route('post.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('post.index')
                ->withErrors(['error' => 'Failed to delete post catalogue: ' . $e->getMessage()]);
        }
    }

    private function config()
    {
        return [
            'js'=>[
                'css2/js/plugins/switchery/switchery.js',
            ],
            'css'=>['css2/css/plugins/switchery/switchery.css'],
        ];
    }


}