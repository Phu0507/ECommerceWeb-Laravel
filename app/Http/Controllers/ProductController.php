<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\AttributeCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $product;
    protected $attributeCategory;

    public function __construct(ProductService $product, AttributeCategoryService $attributeCategory)
    {
        $this->product = $product;
        $this->attributeCategory = $attributeCategory;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.product');
        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('status');
        $search = $request->input('search');

        $productCategory = $this->product->getAllPage($perPage, $publish, $search);
        return view('aadmin.product.product.product_index', compact('config', 'productCategory'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js',
                'css2/library/variant.js'
            ],
        ];
        $config['seo']=config('apps.product');
        $categoryList = $this->product->getCategoryOptions();
        $attributeCategory = $this->attributeCategory->all();
        // Logic to show the form for creating a new user
        return view('aadmin.product.product.product_create', compact('config', 'categoryList', 'attributeCategory'));
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        try {
            
            $this->product->create($data);
            return redirect()->route('product.index')
                ->with('success', 'product catalogue created successfully.');
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
                'css2/library/seo.js',
                'css2/library/variant.js'
            ],
        ];
        $config['seo']=config('apps.product');
        $categoryOptions = $this->product->getProductForEdit($id);
        $categoryList = $this->product->getCategoryOptions();
        $attributeCategory = $this->attributeCategory->all();
        // dd( $categoryOptions);
        if (!$categoryOptions) {
            return redirect()->route('product.index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.product.product.product_update', compact('id', 'config', 'categoryOptions', 'categoryList', 'attributeCategory'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $data = $request->validated();
        // dd( $data);
        try {
            $this->product->update($id, $data);

            return redirect()->route('product.index')
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
            $this->product->delete($id);
            return redirect()->route('product.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('product.index')
                ->withErrors(['error' => 'Failed to delete post catalogue: ' . $e->getMessage()]);
        }
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->input('q', '');
        $results = $this->product->searchByName($keyword);
        return response()->json($results);
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