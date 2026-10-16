<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    protected $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');
        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('status');
        $search = $request->input('search');

        $productCategory = $this->productCategoryService->getAllPage($perPage, $publish, $search);
        return view('aadmin.product.category.product_category_index', compact('config', 'productCategory'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryList = $this->productCategoryService->getCategoryOptions();
        // Logic to show the form for creating a new user
        return view('aadmin.product.category.product_category_create', compact('config', 'categoryList'));
    }

    public function store(ProductCategoryStoreRequest $request)
    {
        $data = $request->validated();
        try {
            $this->productCategoryService->create($data);
            return redirect()->route('product.category.index')
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
        $categoryOptions = $this->productCategoryService->getById($id);
        $categoryList = $this->productCategoryService->getCategoryOptions();
        
        if (!$categoryOptions) {
            return redirect()->route('aadmin.product.category.product_category_index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.product.category.product_category_update', compact('id', 'config', 'categoryOptions', 'categoryList'));
    }

    public function update(ProductCategoryUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->productCategoryService->update($id, $data);
            return redirect()->route('product.category.index')
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
            $this->productCategoryService->delete($id);
            return redirect()->route('product.category.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('product.category.index')
                ->withErrors(['error' => 'Failed to delete post catalogue: ' . $e->getMessage()]);
        }
    }

    //ajax
    public function ajaxGetCategories(Request $request)
    {
        $categories = $this->productCategoryService->getAll()
        ->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        });
        return response()->json($categories);
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