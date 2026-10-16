<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeCategoryStoreRequest;
use App\Http\Requests\AttributeCategoryUpdateRequest;
use App\Services\AttributeCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttributeCategoryController extends Controller
{
    protected $attributeCategoryService;

    public function __construct(AttributeCategoryService $attributeCategoryService)
    {
        $this->attributeCategoryService = $attributeCategoryService;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');
        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('status');
        $search = $request->input('search');

        $attributeCategory = $this->attributeCategoryService->getAllPage($perPage, $publish, $search);
        return view('aadmin.product.attribute_category.attribute_category_index', compact('config', 'attributeCategory'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryList = $this->attributeCategoryService->getCategoryOptions();
        // Logic to show the form for creating a new user
        return view('aadmin.product.attribute_category.attribute_category_create', compact('config', 'categoryList'));
    }

    public function store(AttributeCategoryStoreRequest $request)
    {
        $data = $request->validated();
        try {
            $this->attributeCategoryService->create($data);
            return redirect()->route('attribute.category.index')
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
        $categoryOptions = $this->attributeCategoryService->getById($id);
        $categoryList = $this->attributeCategoryService->getCategoryOptions();
        
        if (!$categoryOptions) {
            return redirect()->route('aadmin.product.attribute_category.attribute_category_index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.product.attribute_category.attribute_category_update', compact('id', 'config', 'categoryOptions', 'categoryList'));
    }

    public function update(AttributeCategoryUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->attributeCategoryService->update($id, $data);
            return redirect()->route('attribute.category.index')
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
            $this->attributeCategoryService->delete($id);
            return redirect()->route('attribute.category.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('attribute.category.index')
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