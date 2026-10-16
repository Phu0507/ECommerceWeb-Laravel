<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostCatalogue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class PostService
{

    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }
    
    public function getAllPage(int $perPage, $publish = null, $search = null)
    {
        $query = Post::select(
                'posts.*',
                'post_language.name as post_name',
                'post_catalogue_language.name as category_name'
            )
            ->join(
                'post_language',
                'posts.id',
                '=',
                'post_language.post_id'
            )->leftJoin('post_catalogue_language', 'posts.catalogue_id', '=', 'post_catalogue_language.post_catalogue_id')
            ->orderBy('posts.order', 'desc');

        // Lọc theo publish nếu có
        if ($publish !== null && $publish !== '') {
            $query->where('posts.publish', $publish);
        }

        // Tìm kiếm theo tên
        if (!empty($search)) {
            $query->where('post_language.name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage)->appends(request()->query());
        return $categories;
    }

    public function getById($id)
    {
        return Post::with('post_language')
        ->find($id);
    }

    public function create(array $data)
    {
            $data['user_id'] = Auth::id();

            // Set order tự động
            $maxOrder = Post::max('order');
            $data['order'] = $maxOrder ? $maxOrder + 1 : 1;

            // Tạo Post
            $post = Post::create($data);

            // Upload ảnh
            if (request()->hasFile('image')) {
                $imagePath = $this->handleImageUpload(request(), null, $post->id);
                if ($imagePath) {
                    $post->update(['image' => $imagePath]);
                }
            }

            // Xử lý ảnh Base64
            if (!empty($data['description'])) {
                $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $post->id);
            }
            if (!empty($data['content'])) {
                $data['content'] = $this->handleBase64ImagesWithDOM($data['content'], $post->id);
            }

            // Attach danh mục chính
            if (!empty($data['catalogue_id'])) {
                $post->post_catalogues()->attach($data['catalogue_id']);
            }

            // Pivot language
            $defaultLanguageId = 7;
            $pivotData = [
                'name'             => $data['name'] ?? null,
                'description'      => $data['description'] ?? null,
                'canonical'        => $data['canonical'] ?? null,
                'meta_title'       => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keyword'     => $data['meta_keyword'] ?? null,
                'content'          => $data['content'] ?? null,
            ];
            $post->languages()->attach($defaultLanguageId, $pivotData);

            //Thêm router (rollback toàn bộ nếu trùng canonical)
            $this->routerService->createRouter(
                $pivotData['canonical'],
                $post->id,
                'App\Http\Controllers\Frontend\PostController'
            );

            return $post;
    }

    public function update($id, array $data)
    {
        $post = Post::findOrFail($id);
        $post->update($data);

        // Upload ảnh mới nếu có
        if (request()->hasFile('image')) {
            $imagePath = $this->handleImageUpload(request(), $post->image, $post->id);
            if ($imagePath) {
                $post->update(['image' => $imagePath]);
            }
        }

        // Xử lý ảnh Base64 trong description & content
        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $post->id);
        }
        if (!empty($data['content'])) {
            $data['content'] = $this->handleBase64ImagesWithDOM($data['content'], $post->id);
        }

        // Cập nhật danh mục chính
        if (!empty($data['catalogue_id'])) {
            $post->post_catalogues()->sync([$data['catalogue_id']]);
        }

        // Cập nhật pivot, ngôn ngữ
        $defaultLanguageId = 7;
        $pivotFields = [
            'name', 'description', 'canonical', 'meta_title', 'meta_description', 'meta_keyword', 'content'
        ];

        $pivot = $post->languages()
            ->where('language_id', $defaultLanguageId)
            ->first()?->pivot;

        $pivotData = [];
        foreach ($pivotFields as $field) {
            $pivotData[$field] = $data[$field] ?? ($pivot->toArray()[$field] ?? null);
        }
        $post->languages()->updateExistingPivot($defaultLanguageId, $pivotData);
        $this->routerService->updateRouter(
            $post->id,
            $pivotData['canonical'],
            'App\Http\Controllers\Frontend\PostController'
        );
        return $post;
    }

    public function delete($id)
    {
        $postCatalogue = Post::findOrFail($id);
        $postCatalogue->delete();
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


    private function handleImageUpload($request, $oldImagePath = null, $postId = null)
    {
        if ($request->hasFile('image') && $postId) {
            $file = $request->file('image');

            $random = Str::random(5);
            $date = now()->format('dmY');
            $originalName = preg_replace('/\s+/', '-', $file->getClientOriginalName());
            $fileName = "{$random}-{$date}-{$originalName}";

            // upload/post/{post_id}/image
            $uploadPath = public_path("upload/post/{$postId}/image");

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $fileName);

            // Xoá ảnh cũ nếu có
            if ($oldImagePath && file_exists(public_path($oldImagePath))) {
                @unlink(public_path($oldImagePath));
            }

            return "upload/post/{$postId}/image/{$fileName}";
        }

        return null;
    }

    protected function handleBase64ImagesWithDOM($html, $postId)
    {
        $folder = public_path('upload/post/' . $postId . '/album');
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
                $img->setAttribute('src', asset('upload/post/' . $postId . '/album/' . $filename));
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
    public function baseQuery($langId = 7)
    {
        return Post::select(
                'posts.*',
                'post_language.name as post_name',
                'post_language.canonical as post_canonical',
                'post_language.meta_description as post_description',
                'post_catalogue_language.name as category_name',
                'post_catalogue_language.canonical as category_canonical'
            )
            ->join('post_language', function($join) use ($langId) {
                $join->on('posts.id', '=', 'post_language.post_id')
                    ->where('post_language.language_id', '=', $langId);
            })
            ->leftJoin('post_catalogue_language', function($join) use ($langId) {
                $join->on('posts.catalogue_id', '=', 'post_catalogue_language.post_catalogue_id')
                    ->where('post_catalogue_language.language_id', '=', $langId);
            });
    }

    public function getLatest($limit = 3, $excludeId = null, $langId = 7)
    {
        $query = $this->baseQuery($langId)
            ->orderBy('posts.created_at', 'desc');

        if (!is_null($excludeId)) {
            $query->where('posts.id', '!=', $excludeId);
        }

        return $query->limit($limit)->get();
    }

    public function getAllPageAndFilter(int $perPage) 
    {
        $query = $this->baseQuery();
        if (request()->filled('keyword')) {
            $keyword = request('keyword');
            $query->where('post_language.name', 'like', "%$keyword%");
        }

        // Filter theo category (cha lấy luôn con)
        if (request()->filled('category')) {
            $catId = request('category');
            $cat = PostCatalogue::find($catId);
            if ($cat) {
                $categoryIds = PostCatalogue::where('lft', '>=', $cat->lft)
                    ->where('rgt', '<=', $cat->rgt)
                    ->pluck('id')
                    ->toArray();

                $query->whereIn('posts.catalogue_id', $categoryIds);
            }
        }

        $posts = $query->paginate($perPage)->appends(request()->query());

        return $posts;
    }

    public function getByCanonical(string $categoryCanonical, string $postCanonical, $langId = 7)
    {
        return Post::select(
                'posts.*',
                'post_language.name as post_name',
                'post_language.content as post_content',
                'post_language.canonical as post_canonical',

                'post_catalogues.id as category_id',
                'post_catalogue_language.name as category_name',
                'post_catalogue_language.canonical as category_canonical',
                'users.name as author_name'
            )
            ->join('post_language', 'posts.id', '=', 'post_language.post_id')
            ->leftJoin('post_catalogues', 'posts.catalogue_id', '=', 'post_catalogues.id')
            ->leftJoin('post_catalogue_language', function($join) use ($langId) {
                $join->on('post_catalogues.id', '=', 'post_catalogue_language.post_catalogue_id')
                    ->where('post_catalogue_language.language_id', '=', $langId);
            })
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->where('post_language.language_id', $langId)
            ->where('post_language.canonical', $postCanonical)
            ->where('post_catalogue_language.canonical', $categoryCanonical)
            ->firstOrFail();
    }

}