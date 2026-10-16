<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCatalogueRequest;
use App\Http\Requests\UpdatePostCatalogueRequest;
use App\Services\PostCatalogueService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PostCatalogueController extends Controller
{
    protected $postCatalogueService;

    public function __construct(PostCatalogueService $postCatalogueService)
    {
        $this->postCatalogueService = $postCatalogueService;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');


        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('publish');
        $search = $request->input('search');

        $postCatalogues = $this->postCatalogueService->getAllPage($perPage, $publish, $search);
        return view('aadmin.post.catalogue.post_catalogue_index', compact('config', 'postCatalogues'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryList = $this->postCatalogueService->getCategoryOptions();
        // Logic to show the form for creating a new user
        return view('aadmin.post.catalogue.post_catalogue_create', compact('config', 'categoryList'));
    }

    public function store(PostCatalogueRequest $request)
    {
        $data = $request->validated();
        try {
            $this->postCatalogueService->create($data);
            return redirect()->route('post.catalogue.index')
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
        $categoryOptions = $this->postCatalogueService->getById($id);
        $categoryList = $this->postCatalogueService->getCategoryOptions();
        
        if (!$categoryOptions) {
            return redirect()->route('aadmin.post.catalogue.index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.post.catalogue.post_catalogue_update', compact('id', 'config', 'categoryOptions', 'categoryList'));
    }

    public function update(UpdatePostCatalogueRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->postCatalogueService->update($id, $data);
            return redirect()->route('post.catalogue.index')
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
            $this->postCatalogueService->delete($id);
            return redirect()->route('post.catalogue.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('post.catalogue.index')
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