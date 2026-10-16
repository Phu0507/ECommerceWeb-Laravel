<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService
{
    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }
    
    public function getAllPage(int $perPage, $status = null, $search = null)
    {
        $query = Attribute::select(
                'attributes.*',
                'attribute_language.name as attribute_name',
                'attribute_category_language.name as category_name'
            )
            ->join(
                'attribute_language',
                'attributes.id',
                '=',
                'attribute_language.attribute_id'
            )->leftJoin('attribute_category_language', 'attributes.category_id', '=', 'attribute_category_language.attribute_category_id')
            ->orderBy('attributes.order', 'desc');

        // Lọc theo publish nếu có
        if ($status !== null && $status !== '') {
            $query->where('attributes.status', $status);
        }

        // Tìm kiếm theo tên
        if (!empty($search)) {
            $query->where('attribute_language.name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage)->appends(request()->query());
        return $categories;
    }

    public function getById($id)
    {
        return Attribute::with('attribute_language')
        ->find($id);
    }

    public function getValuesByCategory($categoryId)
    {
        return Attribute::whereHas('attribute_category', function ($query) use ($categoryId) {
            $query->where('attribute_category_id', $categoryId);
        })->with('attribute_language')->get();
    }


    public function create(array $data)
    {
            $data['user_id'] = Auth::id();

            // Set order tự động
            $maxOrder = Attribute::max('order');
            $data['order'] = $maxOrder ? $maxOrder + 1 : 1;

            // Tạo Post
            $attribute = Attribute::create($data);


            // Xử lý ảnh Base64
            if (!empty($data['description'])) {
                $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $attribute->id);
            }

            // Attach danh mục chính
            if (!empty($data['category_id'])) {
                $attribute->attribute_category()->attach($data['category_id']);
            }

            // Pivot language
            $defaultLanguageId = 7;
            $pivotData = [
                'name'             => $data['name'] ?? null,
                'description'      => $data['description'] ?? null,
                'canonical'        => $data['canonical'] ?? null,
            ];
            $attribute->languages()->attach($defaultLanguageId, $pivotData);

            //Thêm router (rollback toàn bộ nếu trùng canonical)
            $this->routerService->createRouter(
                $pivotData['canonical'],
                $attribute->id,
                'App\Http\Controllers\Frontend\AttributeController'
            );

            return $attribute;
    }

    public function update($id, array $data)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->update($data);


        // Xử lý ảnh Base64 trong description & content
        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $attribute->id);
        }

        // Cập nhật danh mục chính
        if (!empty($data['category_id'])) {
            $attribute->attribute_category()->sync([$data['category_id']]);
        }

        // Cập nhật pivot, ngôn ngữ
        $defaultLanguageId = 7;
        $pivotFields = [
            'name', 'description', 'canonical'
        ];

        $pivot = $attribute->languages()
            ->where('language_id', $defaultLanguageId)
            ->first()?->pivot;

        $pivotData = [];
        foreach ($pivotFields as $field) {
            $pivotData[$field] = $data[$field] ?? ($pivot->toArray()[$field] ?? null);
        }
        $attribute->languages()->updateExistingPivot($defaultLanguageId, $pivotData);
        $this->routerService->updateRouter(
            $attribute->id,
            $pivotData['canonical'],
            'App\Http\Controllers\Frontend\AttributeController'
        );
        return $attribute;
    }

    public function delete($id)
    {
        $postCatalogue = Attribute::findOrFail($id);
        $postCatalogue->delete();
        return true;
    }

    public function getCategoryOptions()
    {
        $categories = AttributeCategory::select(
                'attribute_category.id',
                'attribute_category.level',
                'attribute_category.lft',
                'attribute_category_language.name'
            )
            ->join(
                'attribute_category_language',
                'attribute_category.id',
                '=',
                'attribute_category_language.attribute_category_id'
            )
            ->orderBy('attribute_category.lft', 'asc')
            ->get();
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('|--', max(0, $category->level - 1));
            $options[$category->id] = $prefix . ' ' . $category->name;
        }
        return $options;
    }

    protected function handleBase64ImagesWithDOM($html, $postId)
    {
        $folder = public_path('upload/attribute/' . $postId . '/album');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/^data:image\/(\w+);base64,/', $src, $matches)) {
                $ext = $matches[1];
                $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $src));
                $filename = Str::random(5) . '_' . date('YmdHis') . '.' . $ext;
                $filePath = $folder . '/' . $filename;

                file_put_contents($filePath, $data);

                // Đổi src trong HTML sang đường dẫn ảnh thực tế
                $img->setAttribute('src', asset('upload/attribute/' . $postId . '/album/' . $filename));
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $newHtml = '';
        foreach ($body->childNodes as $child) {
            $newHtml .= $dom->saveHTML($child);
        }

        return $newHtml;
    }
}