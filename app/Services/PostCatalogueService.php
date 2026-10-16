<?php

namespace App\Services;

use App\Models\PostCatalogue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class PostCatalogueService
{
    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }
    
    public function getAllPage(int $perPage, $publish = null, $search = null)
    {
        $query = PostCatalogue::select(
                'post_catalogues.id',
                'post_catalogues.level',
                'post_catalogues.lft',
                'post_catalogues.image',
                'post_catalogues.publish',
                'post_catalogue_language.name'
            )
            ->join(
                'post_catalogue_language',
                'post_catalogues.id',
                '=',
                'post_catalogue_language.post_catalogue_id'
            )
            ->orderBy('post_catalogues.lft', 'asc');

        // Lọc theo publish nếu có
        if ($publish !== null && $publish !== '') {
            $query->where('post_catalogues.publish', $publish);
        }

        // Tìm kiếm theo tên
        if (!empty($search)) {
            $query->where('post_catalogue_language.name', 'like', '%' . $search . '%');
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
        return PostCatalogue::with('post_catalogue_language')
        ->find($id);
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id(); // thêm trước khi create
         // Tạo PostCatalogue mới
        $postCatalogue = PostCatalogue::create($data);

        if (request()->hasFile('image')) {
            $imagePath = $this->handleImageUpload(request(), null, $postCatalogue->id);
            if ($imagePath) {
                $postCatalogue->update(['image' => $imagePath]);
            }
        }

        // Xử lý ảnh Base64 trong description
            if (!empty($data['description'])) {
                $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $postCatalogue->id);
            }
            if (!empty($data['content'])) {
                $data['content'] = $this->handleBase64ImagesWithDOM($data['content'], $postCatalogue->id);
            }
        // Lấy language_id mặc định (ở đây là 1)
        $defaultLanguageId = 7;

        // Dữ liệu cho bảng pivot
        $pivotData = [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'canonical' => $data['canonical'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keyword' => $data['meta_keyword'] ?? null,
            'content' => $data['content'] ?? null,
        ];

        // Thêm vào bảng pivot (post_catalogue_language)
        $postCatalogue->languages()->attach($defaultLanguageId, $pivotData);
        $this->routerService->createRouter(
            $pivotData['canonical'],
            $postCatalogue->id,
            'App\Http\Controllers\Frontend\PostCatalogueController'
        );
        $this->rebuildTree();
        return $postCatalogue;
    }

    public function update($id, array $data)
    {
        $postCatalogue = PostCatalogue::findOrFail($id);

        // Kiểm tra vòng lặp parent_id
        if (isset($data['parent_id']) && $data['parent_id'] != $postCatalogue->parent_id) {
            $descendantIds = [];
            $this->getDescendantIds($postCatalogue->id, $descendantIds);

            if ($data['parent_id'] == $postCatalogue->id || in_array($data['parent_id'], $descendantIds)) {
                throw new \Exception('Invalid parent_id: cannot set a node as child of itself or its descendant.');
            }
        }
        $postCatalogue->update($data);
        // Upload ảnh mới nếu có
        if (request()->hasFile('image')) {
            $imagePath = $this->handleImageUpload(request(), $postCatalogue->image, $postCatalogue->id);
            if ($imagePath) {
                $postCatalogue->update(['image' => $imagePath]);
            }
        }
        // Xử lý ảnh Base64 trong description & content
        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $postCatalogue->id);
        }
        if (!empty($data['content'])) {
            $data['content'] = $this->handleBase64ImagesWithDOM($data['content'], $postCatalogue->id);
        }
        // Cập nhật pivot
        $defaultLanguageId = 7;
        if ($pivot = $postCatalogue->languages()->where('language_id', $defaultLanguageId)->first()?->pivot) {
            $pivotFields = ['name', 'description', 'canonical', 'meta_title', 'meta_description', 'meta_keyword', 'content'];
            // Chỉ lấy các trường quan trọng từ $data, merge với pivot cũ nhưng loại bỏ created_at/updated_at
            $pivotData = array_merge(
                array_intersect_key($pivot->toArray(), array_flip($pivotFields)), 
                array_intersect_key($data, array_flip($pivotFields))
            );
            $postCatalogue->languages()->syncWithoutDetaching([
                $defaultLanguageId => $pivotData
            ]);
            
            if (!empty($pivotData['canonical'])) {
                $this->routerService->updateRouter(
                    $postCatalogue->id,
                    $pivotData['canonical'],
                    'App\Http\Controllers\Frontend\PostCatalogueController'
                );
            }
        }

        
        // Cập nhật lại cấu trúc cây
        $this->rebuildTree();
        return $postCatalogue;
    }

    public function delete($id)
    {
        $postCatalogue = PostCatalogue::findOrFail($id);
        // Kiểm tra con
        $descendantIds = [];
        $this->getDescendantIds($postCatalogue->id, $descendantIds);

        if (!empty($descendantIds)) {
            throw new \Exception('Cannot delete this category because it has child nodes.');
        }
        // Xóa node
        $postCatalogue->delete();
        // Xóa hẳn
        // $postCatalogue->forceDelete();

        // Cập nhật lại cấu trúc cây
        $this->rebuildTree();
        return true;
    }

    public function getCategoryOptions()
    {
        $categories = PostCatalogue::select(
                'post_catalogues.id',
                'post_catalogues.level',
                'post_catalogues.lft',
                'post_catalogue_language.name'
            )
            ->join(
                'post_catalogue_language',
                'post_catalogues.id',
                '=',
                'post_catalogue_language.post_catalogue_id'
            )
            ->orderBy('post_catalogues.lft', 'asc')
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
        $nodes = PostCatalogue::where('parent_id', $parentId)
                    ->orderBy('order', 'asc')
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
        $children = PostCatalogue::where('parent_id', $parentId)->pluck('id');
        foreach ($children as $childId) {
            $descendantIds[] = $childId;
            $this->getDescendantIds($childId, $descendantIds);
        }
    }


    private function handleImageUpload($request, $oldImagePath = null, $postId = null)
    {
        if ($request->hasFile('image') && $postId) {
            $file = $request->file('image');

            $random = Str::random(5);
            $date = now()->format('dmY');
            $originalName = preg_replace('/\s+/', '-', $file->getClientOriginalName());
            $fileName = "{$random}-{$date}-{$originalName}";

            // upload/post/{post_id}/image
            $uploadPath = public_path("upload/post_catalogue/{$postId}/image");

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $fileName);

            // Xoá ảnh cũ nếu có
            if ($oldImagePath && file_exists(public_path($oldImagePath))) {
                @unlink(public_path($oldImagePath));
            }

            return "upload/post_catalogue/{$postId}/image/{$fileName}";
        }

        return null;
    }

    protected function handleBase64ImagesWithDOM($html, $postId)
    {
        $folder = public_path('upload/post_catalogue/' . $postId . '/album');
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
                $img->setAttribute('src', asset('upload/post_catalogue/' . $postId . '/album/' . $filename));
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $newHtml = '';
        foreach ($body->childNodes as $child) {
            $newHtml .= $dom->saveHTML($child);
        }

        return $newHtml;
    }

    //frontend
    public function getAll()
    {
        $currentLangId = 7;

        $categories = PostCatalogue::select('post_catalogues.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(DISTINCT posts.id)')
                    ->from('post_catalogues as child')
                    ->join('post_catalogue_post', 'child.id', '=', 'post_catalogue_post.post_catalogue_id')
                    ->join('posts', 'posts.id', '=', 'post_catalogue_post.post_id')
                    ->whereNull('posts.deleted_at')
                    ->whereColumn('child.lft', '>=', 'post_catalogues.lft')
                    ->whereColumn('child.rgt', '<=', 'post_catalogues.rgt');
            }, 'posts_count')
            ->with(['languages' => function ($q) use ($currentLangId) {
                $q->where('language_id', $currentLangId);
            }])
            ->orderBy('post_catalogues.lft', 'asc')
            ->get();

        return $categories;
    }

}