<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionStoreRequest;
use App\Http\Requests\PromotionUpdateRequest;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    protected $promotion;

    public function __construct(PromotionService $promotion)
    {
        $this->promotion = $promotion;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $config = $this->config();
        $config['seo'] = config('apps.postcatalogue');
        $perPage = (int) $request->input('perpage', 10);
        $publish = $request->input('status');
        $search = $request->input('search');

        $promotionCategory = $this->promotion->getAllPage($perPage, $publish, $search);
        // dd($promotionCategory);
        return view('aadmin.promotion.promotion.promotion_index', compact('config', 'promotionCategory'));
    }

    public function create()
    {
        $config = [
            'js' => [
                'css2/library/seo.js',
                'css2/library/promotion.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        // Logic to show the form for creating a new user
        return view('aadmin.promotion.promotion.promotion_create', compact('config'));
    }

    public function store(PromotionStoreRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        try {
            
            $this->promotion->create($data);
            return redirect()->route('promotion.index')
                ->with('success', 'Promotion created successfully.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Error creating promotion: ' . $errorMessage);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create promotion: ' . $errorMessage]);
        }
    }

    public function edit($id)
    {
        $config = [
            'js' => [
                'css2/library/seo.js',
                'css2/library/promotion.js'
            ],
        ];
        $config['seo']=config('apps.postcatalogue');
        $promotions = $this->promotion->getById($id);
        // dd($promotions);
        if (!$promotions) {
            return redirect()->route('promotion.index')->withErrors(['error' => 'Post catalogue not found.']);
        }
        return view('aadmin.promotion.promotion.promotion_update', compact('id', 'config', 'promotions'));
    }

    public function update(PromotionUpdateRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $this->promotion->update($id, $data);
            return redirect()->route('promotion.index')
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
            $this->promotion->delete($id);
            return redirect()->route('promotion.index')
                ->with('success', 'Post catalogue deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('promotion.index')
                ->withErrors(['error' => 'Failed to delete post catalogue: ' . $e->getMessage()]);
        }
    }


    public function getValues($categoryId)
    {
        $attributes = $this->promotion->getValuesByCategory($categoryId);
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