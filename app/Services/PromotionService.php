<?php

namespace App\Services;

use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class PromotionService
 * @package App\Services
 */
class PromotionService
{
    protected $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }
    
    public function getAllPage(int $perPage, $status = null, $search = null)
    {
        $query = Promotion::with('discounts', 'customerConditions', 'products')
            ->orderBy('start_date', 'desc');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $promotions = $query->paginate($perPage)->appends(request()->query());
        $promotions->getCollection()->transform(function ($promotion) {
            return [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'description' => $promotion->description,
                'discount_type' => $promotion->discount_type,
                'customer_option' => $promotion->customer_option,
                'promotion_type' => $promotion->promotion_type,
                'start_date' => $promotion->start_date,
                'end_date' => $promotion->end_date,
                'status' => $promotion->status,
                'discounts' => $promotion->discounts->map(function ($d) {
                    return [
                        'discount_value' => $d->discount_value,
                        'order_amount_from' => $d->order_amount_from,
                        'order_amount_to' => $d->order_amount_to,
                        'min_quantity' => $d->min_quantity,
                        'max_quantity' => $d->max_quantity,
                    ];
                }),
                'customer_conditions' => $promotion->customerConditions,
                'products' => $promotion->products,
            ];
        });
        return $promotions;
    }

    public function getById($id)
    {
        return Promotion::with([
        'discounts',           
        'customerConditions',   
        'products', 
        ])->find($id);
    }

    public function getValuesByCategory($categoryId)
    {
        return Promotion::whereHas('attribute_category', function ($query) use ($categoryId) {
            $query->where('attribute_category_id', $categoryId);
        })->with('attribute_language')->get();
    }


    public function create(array $data)
    {
        // Dùng transaction để rollback nếu có lỗi
        return DB::transaction(function () use ($data) {

            //Tạo Promotion chính
            $promotion = Promotion::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'] ?? null,
                'promotion_type' => $data['promotion_type'],
                'discount_type' => $data['discount_type'],
                'customer_option' => $data['customer_option'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);

            //Tạo Promotion Discounts (nhiều mức)
            if (!empty($data['discount'])) {
                foreach ($data['discount'] as $index => $value) {
                    $promotion->discounts()->create([
                        'discount_value' => $value,
                        'order_amount_from' => $data['order_amount_from'][$index] ?? null,
                        'order_amount_to' => $data['order_amount_to'][$index] ?? null,
                        'min_quantity' => $data['min_quantity'] ?? null,
                        'max_quantity' => $data['max_quantity'] ?? null,
                    ]);
                }
            }

            //Tạo Customer Conditions nếu customer_option = 'custom'
            if (($data['customer_option'] ?? null) === 'custom') {
                $gender = $data['gender'] ?? null;
                $ageGroups = $data['age_group'] ?? [];
                $userRoles = $data['customer_group'] ?? [];

                // Nếu gender chỉ 1 giá trị thì để vào mảng cho dễ xử lý chung
                $genders = $gender ? [$gender] : [null];
                $ageGroups = !empty($ageGroups) ? $ageGroups : [null];
                $userRoles = !empty($userRoles) ? $userRoles : [null];

                foreach ($genders as $g) {
                    foreach ($ageGroups as $age) {
                        foreach ($userRoles as $role) {
                            $promotion->customerConditions()->create([
                                'gender' => $g,
                                'age_group' => $age,
                                'user_role_id' => $role,
                            ]);
                        }
                    }
                }
            }
            //Tạo Product liên quan nếu promotion_type = 'product'
            if (($data['promotion_type'] ?? null) === 'product') {
                $productRange = $data['product-range-select'] ?? null;

                if ($productRange === 'products') {
                    foreach ($data['ids'] as $productId) {
                        $promotion->products()->create([
                            'product_id' => $productId,
                            'apply_all' => false,
                        ]);
                    }
                } elseif ($productRange === 'category') {
                    foreach ($data['ids'] as $categoryId) {
                        $promotion->products()->create([
                            'category_id' => $categoryId,
                            'apply_all' => false,
                        ]);
                    }
                } elseif ($productRange === 'All') {
                    $promotion->products()->create([
                        'apply_all' => true,
                    ]);
                }
            }
            return $promotion;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $promotion = Promotion::findOrFail($id);
            $promotion->update([
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'] ?? null,
                'promotion_type' => $data['promotion_type'],
                'discount_type' => $data['discount_type'],
                'customer_option' => $data['customer_option'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);
            $promotion->discounts()->delete();
            $promotion->customerConditions()->delete();
            $promotion->products()->delete();
            if (!empty($data['discount'])) {
                foreach ($data['discount'] as $index => $value) {
                    $promotion->discounts()->create([
                        'discount_value' => $value,
                        'order_amount_from' => $data['order_amount_from'][$index] ?? null,
                        'order_amount_to' => $data['order_amount_to'][$index] ?? null,
                        'min_quantity' => $data['min_quantity'] ?? null,
                        'max_quantity' => $data['max_quantity'] ?? null,
                    ]);
                }
            }

            if (($data['customer_option'] ?? null) === 'custom') {
                $genders = !empty($data['gender']) ? [$data['gender']] : [null];
                $ageGroups = !empty($data['age_group']) ? $data['age_group'] : [null];
                $userRoles = !empty($data['customer_group']) ? $data['customer_group'] : [null];

                foreach ($genders as $g) {
                    foreach ($ageGroups as $age) {
                        foreach ($userRoles as $role) {
                            $promotion->customerConditions()->create([
                                'gender' => $g,
                                'age_group' => $age,
                                'user_role_id' => $role,
                            ]);
                        }
                    }
                }
            }
            // Tạo Product liên quan nếu promotion_type = 'product'
            if (($data['promotion_type'] ?? null) === 'product') {
                $productRange = $data['product-range-select'] ?? null;

                if ($productRange === 'products') {
                    foreach ($data['ids'] as $productId) {
                        $promotion->products()->create([
                            'product_id' => $productId,
                            'apply_all' => false,
                        ]);
                    }
                } elseif ($productRange === 'category') {
                    foreach ($data['ids'] as $categoryId) {
                        $promotion->products()->create([
                            'category_id' => $categoryId,
                            'apply_all' => false,
                        ]);
                    }
                } elseif ($productRange === 'All') {
                    $promotion->products()->create([
                        'apply_all' => true,
                    ]);
                }
            }
            return $promotion;
        });
    }


    public function delete($id)
    {
        $postCatalogue = Promotion::findOrFail($id);
        $postCatalogue->delete();
        return true;
    }
}