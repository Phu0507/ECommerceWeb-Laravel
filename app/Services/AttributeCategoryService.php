<?php

namespace App\Services;

use App\Models\AttributeCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class AttributeCategoryService
 * @package App\Services
 */
class AttributeCategoryService
{
    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }

    public function all()
    {
        return AttributeCategory::with(['attribute_category_language' => function($query) {
                $query->select('attribute_category_id', 'name');
            }])->get();
    }
    
    public function getAllPage(int $perPage, $status = null, $search = null)
    {
        $query = AttributeCategory::select(
                'attribute_category.id',
                'attribute_category.level',
                'attribute_category.lft',
                'attribute_category.status',
                'attribute_category_language.name'
            )
            ->join(
                'attribute_category_language',
                'attribute_category.id',
                '=',
                'attribute_category_language.attribute_category_id'
            )
            ->orderBy('attribute_category.lft', 'asc');

        // Lọc theo publish nếu có
        if ($status !== null && $status !== '') {
            $query->where('attribute_category.status', $status);
        }

        // Tìm kiếm theo tên
        if (!empty($search)) {
            $query->where('attribute_category_language.name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage)->appends(request()->query());

        // Xử lý tạo prefix để thụt vào
        foreach ($categories as $category) {
            $prefix = str_repeat('|--', max(0, $category->level - 1));
            $category->display_name = $prefix . ' ' . $category->name;
        }

        return $categories;
    }

    public function getById($id)
    {
        return AttributeCategory::with('attribute_category_language')
        ->find($id);
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id(); // thêm trước khi create
         // Tạo PostCatalogue mới
        $attributeCategory = AttributeCategory::create($data);

        // Xử lý ảnh Base64 trong description
            if (!empty($data['description'])) {
                $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $attributeCategory->id);
            }
        // Lấy language_id mặc định (ở đây là 1)
        $defaultLanguageId = 7;

        // Dữ liệu cho bảng pivot
        $pivotData = [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'canonical' => $data['canonical'] ?? null,
        ];

        // Thêm vào bảng pivot (post_catalogue_language)
        $attributeCategory->languages()->attach($defaultLanguageId, $pivotData);
        $this->routerService->createRouter(
            $pivotData['canonical'],
            $attributeCategory->id,
            'App\Http\Controllers\Frontend\AttributeCategoryController'
        );
        $this->rebuildTree();
        return $attributeCategory;
    }

    public function update($id, array $data)
    {
        $attributeCategory = AttributeCategory::findOrFail($id);

        // Kiểm tra vòng lặp parent_id
        if (isset($data['parent_id']) && $data['parent_id'] != $attributeCategory->parent_id) {
            $descendantIds = [];
            $this->getDescendantIds($attributeCategory->id, $descendantIds);

            if ($data['parent_id'] == $attributeCategory->id || in_array($data['parent_id'], $descendantIds)) {
                throw new \Exception('Invalid parent_id: cannot set a node as child of itself or its descendant.');
            }
        }
        $attributeCategory->update($data);
        // Xử lý ảnh Base64 trong description & content
        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $attributeCategory->id);
        }
        // Cập nhật pivot
        $defaultLanguageId = 7;
        if ($pivot = $attributeCategory->languages()->where('language_id', $defaultLanguageId)->first()?->pivot) {
            $pivotFields = ['name', 'description', 'canonical'];
            // Chỉ lấy các trường quan trọng từ $data, merge với pivot cũ nhưng loại bỏ created_at/updated_at
            $pivotData = array_merge(
                array_intersect_key($pivot->toArray(), array_flip($pivotFields)), 
                array_intersect_key($data, array_flip($pivotFields))
            );
            $attributeCategory->languages()->syncWithoutDetaching([
                $defaultLanguageId => $pivotData
            ]);
            
            if (!empty($pivotData['canonical'])) {
                $this->routerService->updateRouter(
                    $attributeCategory->id,
                    $pivotData['canonical'],
                    'App\Http\Controllers\Frontend\AttributeCategoryController'
                );
            }
        }

        
        // Cập nhật lại cấu trúc cây
        $this->rebuildTree();
        return $attributeCategory;
    }

    public function delete($id)
    {
        $attributeCategory = AttributeCategory::findOrFail($id);
        // Kiểm tra con
        $descendantIds = [];
        $this->getDescendantIds($attributeCategory->id, $descendantIds);

        if (!empty($descendantIds)) {
            throw new \Exception('Cannot delete this category because it has child nodes.');
        }
        // Xóa node
        $attributeCategory->forceDelete();
        // Xóa hẳn
        // $postCatalogue->forceDelete();

        // Cập nhật lại cấu trúc cây
        $this->rebuildTree();
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

    //nested set

    public function rebuildTree()
    {
        $counter = 1;
        $this->rebuildNode(0, 0, $counter);
    }
    protected function rebuildNode($parentId, $level, int &$counter)
    {
        // Lấy tất cả node con của parent hiện tại, sắp xếp theo order (nếu có)
        $nodes = AttributeCategory::where('parent_id', $parentId)
                    ->orderBy('lft', 'asc')
                    ->get();

        foreach ($nodes as $node) {
            // Bắt đầu lft
            $node->lft = $counter++;
            $node->level = $level+1;

            // Đệ quy cho các node con
            $this->rebuildNode($node->id, $level + 1, $counter);

            // Kết thúc rgt
            $node->rgt = $counter++;
            $node->save();
        }
    }

    protected function getDescendantIds($parentId, array &$descendantIds)
    {
        $children = AttributeCategory::where('parent_id', $parentId)->pluck('id');
        foreach ($children as $childId) {
            $descendantIds[] = $childId;
            $this->getDescendantIds($childId, $descendantIds);
        }
    }


    protected function handleBase64ImagesWithDOM($html, $postId)
    {
        $folder = public_path('upload/attribute_category/' . $postId . '/album');
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
                $img->setAttribute('src', asset('upload/attribute_category/' . $postId . '/album/' . $filename));
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