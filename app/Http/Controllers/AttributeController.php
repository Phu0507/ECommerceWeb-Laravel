<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeUpdateRequest;
use App\Services\AttributeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttributeController extends Controller
{
    protected $attribute;

    public function __construct(AttributeService $attribute)
    {
        $this->attribute = $attribute;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');


        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('status');
        $search = $request->input('search');

        $attributeCategory = $this->attribute->getAllPage($perPage, $publish, $search);
        return view('aadmin.product.attribute.attribute_index', compact('config', 'attributeCategory'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $categoryList = $this->attribute->getCategoryOptions();
        // Logic to show the form for creating a new user
        return view('aadmin.product.attribute.attribute_create', compact('config', 'categoryList'));
    }

    public function store(AttributeStoreRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        try {
            
            $this->attribute->create($data);
            return redirect()->route('attribute.index')
                ->with('success', 'attribute catalogue created successfully.');
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
        $categoryOptions = $this->attribute->getById($id);
        $categoryList = $this->attribute->getCategoryOptions();
        
        if (!$categoryOptions) {
            return redirect()->route('attribute.index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.product.attribute.attribute_update', compact('id', 'config', 'categoryOptions', 'categoryList'));
    }

    public function update(AttributeUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->attribute->update($id, $data);

            return redirect()->route('attribute.index')
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
            $this->attribute->delete($id);
            return redirect()->route('attribute.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            // Nén lỗi ra message
            return redirect()->route('attribute.index')
                ->withErrors(['error' => 'Failed to delete post catalogue: ' . $e->getMessage()]);
        }
    }


    public function getValues($categoryId)
    {
        $attributes = $this->attribute->getValuesByCategory($categoryId);
        $result = $attributes->map(function($attr){
            return [
                'id' => $attr->id,
                'name' => $attr->attribute_language->first()->name ?? 'N/A',
            ];
    });

    return response()->json($result);
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