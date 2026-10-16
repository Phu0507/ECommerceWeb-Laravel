<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductRating;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
/**
 * Class ProductService
 * @package App\Services
 */
class ProductService
{
    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }
    
    public function getAllPage(int $perPage, $status = null, $search = null)
    {
        $query = Product::select(
                'products.*',
                'product_language.name as product_name',
                'product_category_language.name as category_name'
            )
            ->join(
                'product_language',
                'products.id',
                '=',
                'product_language.product_id'
            )->leftJoin('product_category_language', 'products.category_id', '=', 'product_category_language.product_category_id')
            ->orderBy('products.order', 'desc');

        // Lọc theo publish nếu có
        if ($status !== null && $status !== '') {
            $query->where('products.status', $status);
        }

        // Tìm kiếm theo tên
        if (!empty($search)) {
            $query->where('product_language.name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage)->appends(request()->query());
        return $categories;
    }

    public function getById($id)
    {
        return Product::with('product_language')
        ->find($id);
    }

    public function getProductForEdit($id)
    {
        $product = Product::with([
            'product_language',
            'product_variants.attributes.attribute_category'
        ])->find($id);

        return $this->buildProductData($product);
    }


    public function create(array $data)
    {
        $data['user_id'] = Auth::id();
        $maxOrder = Product::max('order');
        $data['order'] = $maxOrder ? $maxOrder + 1 : 1;
        $product = Product::create(array_merge($data, [
            'base_price'    => $data['base_price'] ?? 0,
            'base_quantity' => $data['base_quantity'] ?? 0,
        ]));

        if (request()->hasFile('image')) {
            if ($imagePath = $this->handleImageUpload(request(), null, $product->id)) {
                $product->update(['image' => $imagePath]);
            }
        }

        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $product->id);
        }

        // Attach danh mục chính
        if (!empty($data['category_id'])) {
            $product->product_category()->attach($data['category_id']);
        }

        // Pivot language
        $defaultLanguageId = 7;
        $pivotData = [
            'name'        => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'canonical'   => $data['canonical'] ?? null,
            'meta_title'  => $data['meta_title'] ?? null,
        ];
        $product->languages()->attach($defaultLanguageId, $pivotData);

        $this->routerService->createRouter(
            $pivotData['canonical'],
            $product->id,
            'App\Http\Controllers\Frontend\ProductController'
        );

        // Nếu có biến thể thì lưu
        if (!empty($data['variant_sku']) && is_array($data['variant_sku'])) {
            foreach ($data['variant_sku'] as $index => $sku) {
                if (empty($sku)) continue;

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku'        => $sku,
                    'quantity'   => $data['variant_quantity'][$index] ?? 0,
                    'price'      => $data['variant_price'][$index] ?? 0,
                    'user_id'    => Auth::id(),
                ]);

                $variant->languages()->attach($defaultLanguageId, [
                    'name' => $data['variant_attributes']['name'][$index] ?? '',
                ]);

                $attrString = $data['variant_attributes']['id'][$index] ?? '';
                $attributeIds = $attrString ? explode(',', $attrString) : [];
                if (!empty($attributeIds)) {
                    $variant->attributes()->sync($attributeIds);
                }

                if (!empty($data['variant_image'][$sku])) {
                    $variant->album = $this->handleVariantImages(
                        $data['variant_image'][$sku],
                        $variant->id,
                        $variant->album,
                        $product->id
                    );
                    $variant->save();
                }
            }
        }
        return $product;
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);

        $product->update(array_merge($data, [
            'base_price'    => $data['base_price'] ?? $product->base_price,
            'base_quantity' => $data['base_quantity'] ?? $product->base_quantity,
        ]));

        if (request()->hasFile('image')) {
            if ($imagePath = $this->handleImageUpload(request(), $product->image, $product->id)) {
                $product->update(['image' => $imagePath]);
            }
        }

        if (!empty($data['description'])) {
            $data['description'] = $this->handleBase64ImagesWithDOM($data['description'], $product->id);
        }

        if (!empty($data['category_id'])) {
            $product->product_category()->sync([$data['category_id']]);
        }

        $defaultLanguageId = 7;
        $pivotFields = ['name', 'description', 'canonical', 'meta_title'];
        $pivot = $product->languages()->where('language_id', $defaultLanguageId)->first()?->pivot;
        $pivotData = [];
        foreach ($pivotFields as $field) {
            $pivotData[$field] = $data[$field] ?? ($pivot?->{$field} ?? null);
        }
        $product->languages()->updateExistingPivot($defaultLanguageId, $pivotData);

        $this->routerService->updateRouter(
            $product->id,
            $pivotData['canonical'],
            'App\Http\Controllers\Frontend\ProductController'
        );

        //XỬ LÝ VARIANTS
        $oldVariants = $product->product_variants()->get()->keyBy('sku'); // map SKU -> model
        $submittedSkus = $data['variant_sku'] ?? [];

        foreach ($submittedSkus as $index => $sku) {
            if (empty($sku)) continue;

            $variantData = [
                'quantity' => $data['variant_quantity'][$index] ?? 0,
                'price'    => $data['variant_price'][$index] ?? 0,
                'user_id'  => Auth::id(),
            ];

            if (isset($oldVariants[$sku])) {
                // SKU đã tồn tại -> cập nhật
                $variant = $oldVariants[$sku];
                $variant->update($variantData);

                $variant->languages()->updateExistingPivot($defaultLanguageId, [
                    'name' => $data['variant_attributes']['name'][$index] ?? $variant->languages()->first()?->pivot->name,
                ]);
            } else {
                // SKU mới -> tạo
                $variant = ProductVariant::create(array_merge($variantData, [
                    'product_id' => $product->id,
                    'sku'        => $sku,
                ]));

                $variant->languages()->attach($defaultLanguageId, [
                    'name' => $data['variant_attributes']['name'][$index] ?? '',
                ]);
            }

            $attrString = $data['variant_attributes']['id'][$index] ?? '';
            $attributeIds = $attrString ? explode(',', $attrString) : [];
            $variant->attributes()->sync($attributeIds);
            
            if (!empty($data['variant_image'][$sku])) {
                $variant->album = $this->handleVariantImages(
                    $data['variant_image'][$sku],
                    $variant->id,
                    $variant->album,
                    $product->id
                );
                $variant->save();
            }
        }

        // SKU có trong DB nhưng không còn trong request -> xóa
        $skusToDelete = $oldVariants->keys()->diff($submittedSkus);
        if ($skusToDelete->isNotEmpty()) {
            ProductVariant::where('product_id', $product->id)
                ->whereIn('sku', $skusToDelete)
                ->forceDelete();
        }
        return $product;
    }


    public function delete($id)
    {
        $postCatalogue = Product::findOrFail($id);
        $postCatalogue->forceDelete();
        return true;
    }

    public function getCategoryOptions()
    {
        $categories = ProductCategory::select(
                'product_category.id',
                'product_category.level',
                'product_category.lft',
                'product_category_language.name'
            )
            ->join(
                'product_category_language',
                'product_category.id',
                '=',
                'product_category_language.product_category_id'
            )
            ->orderBy('product_category.lft', 'asc')
            ->get();
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('|--', max(0, $category->level - 1));
            $options[$category->id] = $prefix . ' ' . $category->name;
        }
        return $options;
    }


    public function searchByName(string $keyword, int $limit = 10)
    {
        // Lấy dữ liệu sản phẩm theo tên, giới hạn số lượng trả về
        $products = Product::select(
                'products.id',
                'product_language.name as product_name',
                'products.status'
            )
            ->join('product_language', 'products.id', '=', 'product_language.product_id')
            ->where('product_language.name', 'like', '%' . $keyword . '%')
            ->orderBy('products.order', 'desc')
            ->limit($limit)
            ->get();

        return $products;
    }

    protected function buildProductData($product)
    {
        if (!$product) return null;
        $data = [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'image' => $product->image,
            'album' => $product->album,
            'status' => $product->status,
            'order' => $product->order,
            'user_id' => $product->user_id,
            'base_price' => $product->base_price,
            'base_quantity' => $product->base_quantity,
        ];

        if ($product->product_language->isNotEmpty()) {
            $lang = $product->product_language->first();
            $data['name'] = $lang->name;
            $data['description'] = $lang->description;
            $data['canonical'] = $lang->canonical;
            $data['meta_title'] = $lang->meta_title;
            $data['price'] = $lang->price;
        }

        // Biến thể
        $data['variant_sku'] = [];
        $data['variant_quantity'] = [];
        $data['variant_price'] = [];
        $data['variant_image'] = [];
        $data['variant_attributes'] = [];

        foreach ($product->product_variants as $variant) {
            $variantLang = $variant->languages->where('pivot.language_id', 7)->first();
            $variantName = $variantLang ? $variantLang->pivot->name : '';

            $valueIds = $variant->attributes->pluck('id')->toArray();
            $names = explode(',', $variantName);

            $categoryIds = [];
            foreach ($variant->attributes as $attr) {
                $categoryIds[] = $attr->attribute_category->first()?->id ?? null;
            }

            $data['variant_attributes'][] = [
                'id'         => $variant->id,
                'name'       => $names,
                'value_id'   => $valueIds,
                'category_id'=> $categoryIds
            ];

            $data['variant_sku'][] = $variant->sku;
            $data['variant_quantity'][] = $variant->quantity;
            $data['variant_price'][] = $variant->price;

            $images = $variant->album ? explode(',', $variant->album) : [];
            $images = array_map(fn($img) => asset($img), $images);
            $data['variant_image'][$variant->sku] = $images;
        }

        return $data;
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
            $uploadPath = public_path("upload/product/{$postId}/image");

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $fileName);

            // Xoá ảnh cũ nếu có
            if ($oldImagePath && file_exists(public_path($oldImagePath))) {
                @unlink(public_path($oldImagePath));
            }

            return "upload/product/{$postId}/image/{$fileName}";
        }

        return null;
    }

    protected function handleBase64ImagesWithDOM($html, $postId)
    {
        $folder = public_path('upload/product/' . $postId . '/album');
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
                $img->setAttribute('src', asset('upload/product/' . $postId . '/album/' . $filename));
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $newHtml = '';
        foreach ($body->childNodes as $child) {
            $newHtml .= $dom->saveHTML($child);
        }

        return $newHtml;
    }


    private function handleVariantImages(array $files, $variantId, $oldImages = null, $productId)
    {
        $paths = [];

        // Xóa ảnh cũ nếu có
        if ($oldImages) {
            foreach (explode(',', $oldImages) as $oldImage) {
                $oldPath = public_path($oldImage);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $random = Str::random(5);
                $date = now()->format('dmY');
                $originalName = preg_replace('/\s+/', '-', $file->getClientOriginalName());
                $fileName = "{$random}-{$date}-{$originalName}";

                $uploadPath = public_path("upload/product/{$productId}/variant/{$variantId}");

                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $paths[] = "upload/product/{$productId}/variant/{$variantId}/{$fileName}";
            }
        }
        return implode(',', $paths);
    }

    //Frontend
    public function attachPromotionsToProducts($products)
    {
        $productIds = $products->pluck('id')->toArray();
        $categoryIds = $products->pluck('category_id')->unique()->toArray();
        $now = Carbon::now();

        // Lấy tất cả promotion liên quan, còn hiệu lực và eager load discounts
        $promotions = Promotion::with('discounts')->whereHas('products', function($query) use ($productIds, $categoryIds) {
            $query->whereIn('product_id', $productIds)
                ->orWhereIn('category_id', $categoryIds)
                ->orWhere('apply_all', 1);
        })
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->get();

        // Map promotions vào từng sản phẩm
        $products->map(function($product) use ($promotions) {
            $productPromotions = $promotions->filter(function($promotion) use ($product) {
                if ($promotion->products->contains('product_id', $product->id)) return true;
                if ($promotion->products->contains('category_id', $product->category_id)) return true;
                if ($promotion->products->contains('apply_all', 1)) return true;
                return false;
            });

            $product->promotions = $productPromotions->values();
            return $product;
        });

        return $products;
    }

    public function baseQuery()
    {
        return Product::select(
                'products.*',
                'product_language.name as product_name',
                'product_category_language.name as category_name'
            )
            ->join(
                'product_language',
                'products.id',
                '=',
                'product_language.product_id'
            )
            ->leftJoin(
                'product_category_language',
                'products.category_id',
                '=',
                'product_category_language.product_category_id'
            );
    }

    public function getLatest($limit = 8)
    {
        $products = $this->baseQuery()
        ->orderBy('products.created_at', 'desc')
        ->limit($limit)
        ->get();
        $products = $this->attachPromotionsToProducts($products);

        return $products;
    }

    public function getAllPageAndFilter(int $perPage) 
    {
        $query = $this->baseQuery();
        // Lọc theo nhiều category
        if (request()->has('categories') && is_array(request()->categories)) {
            $query->whereIn('products.category_id', request()->categories);
        }
        // Lọc theo giá
        if (request()->filled('min_price')) {
            $query->where('base_price', '>=', request('min_price'));
        }
        if (request()->filled('max_price')) {
            $query->where('base_price', '<=', request('max_price'));
        }
        // Sắp xếp theo giá
        if (request()->filled('sort')) {
            if (request('sort') === 'asc') {
                $query->orderBy('products.base_price', 'asc');
            } elseif (request('sort') === 'desc') {
                $query->orderBy('products.base_price', 'desc');
            }
        } else {
            $query->orderBy('products.order', 'desc');
        }
        $products = $query->paginate($perPage)->appends(request()->query());

        //promotions
        $products->getCollection()->transform(function($product) {
            return $this->attachPromotionsToProducts(collect([$product]))->first();
        });

        return $products;
    }

    public function getProductDetail($id, $withRelated = true)
    {
        $product = Product::with([
            'product_language',
            'product_variants.attributes.attribute_category'
        ])->find($id);

        $data = $this->buildProductData($product);
        if (!$data) return null;
        $now = now();
        $promotions = Promotion::with('discounts')
            ->whereHas('products', function($q) use ($product) {
                $q->where('product_id', $product->id)
                ->orWhere('category_id', $product->category_id)
                ->orWhere('apply_all', 1);
            })
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $data['promotions'] = $promotions->map(function($promo) {
            return [
                'id'            => $promo->id,
                'name'          => $promo->name,
                'code'          => $promo->code,
                'description'   => $promo->description,
                'promotion_type'=> $promo->promotion_type,
                'discount_type' => $promo->discount_type,
                'start_date'    => $promo->start_date,
                'end_date'      => $promo->end_date,
                'discounts'     => $promo->discounts->map(fn($d) => [
                    'id'                => $d->id,
                    'discount_value'    => $d->discount_value,
                    'order_amount_from' => $d->order_amount_from,
                    'order_amount_to'   => $d->order_amount_to,
                    'min_quantity'      => $d->min_quantity,
                    'max_quantity'      => $d->max_quantity,
                ])
            ];
        });

        if ($withRelated) {
            $relatedProducts = Product::with(['product_language' => function($q) {
            $q->where('language_id', 7);}])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
            $relatedProducts = $this->attachPromotionsToProducts($relatedProducts);
            $data['relatedProducts'] = $relatedProducts;
        }

        return $data;
    }

}