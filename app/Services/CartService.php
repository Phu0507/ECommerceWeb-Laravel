<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart()
    {
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();

            $cart = Cart::firstOrCreate([
                'customer_id' => $customer->id,
            ]);
        } else {
            $sessionId = session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = (string) Str::uuid();
                session(['cart_session_id' => $sessionId]);
            }

            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }
        $cart->load([
            'items.product.translation',
            'items.variant.translation',
        ]);

        return $cart;
    }

    public function addToCart($productId, $variantId = null, $quantity = 1, $price = null, $discount = 0, $finalPrice = null)
    {
        $cart = $this->getCart();
        $query = $cart->items()->where('product_id', $productId);
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }
        $item = $query->first();

        $price = $price ?? 0;
        $finalPrice = $finalPrice ?? $price * $quantity;

        if ($item) {
            $item->quantity += $quantity;
            $item->price = $price;
            $item->discount = $discount;
            $item->final_price = $finalPrice;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'quantity'           => $quantity,
                'price'              => $price,
                'discount'           => $discount,
                'final_price'        => $finalPrice,
            ]);
        }

        $this->updateCartTotals($cart);
        return $cart->fresh('items');
    }

    public function updateCartTotals($cart)
    {
        $cart->load('items');
        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $discount = $cart->items->sum(fn($item) => $item->discount * $item->quantity);
        $total    = $subtotal - $discount;

        $cart->update([
            'subtotal'           => $subtotal,
            'promotion_discount' => $discount,
            'total'              => $total,
        ]);
    }

    public function clearCart()
    {
        $cart = $this->getCart();
        if (!$cart) {
            return;
        }
        $cart->items()->delete();
        $cart->update([
            'subtotal'           => 0,
            'promotion_discount' => 0,
            'total'              => 0,
        ]);
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);
        if (!$item) {
            return null;
        }
        $item->quantity = $quantity;
        $item->save();
        $this->updateCartTotals($cart);
        return [
            'item_total' => number_format($item->final_price * $item->quantity, 0, ',', '.').'₫',
            'cart_subtotal' => number_format($cart->subtotal, 0, ',', '.').'₫',
            'cart_discount' => number_format($cart->promotion_discount, 0, ',', '.').'₫',
            'cart_total' => number_format($cart->total, 0, ',', '.').'₫',
            'cartCount'    => $cart->items->count(),
        ];
    }

    public function removeItemById($itemId)
    {
        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);
        if (!$item) {
            return null;
        }
        $item->delete();
        $this->updateCartTotals($cart);
        return [
            'cart_subtotal' => number_format($cart->subtotal, 0, ',', '.').'₫',
            'cart_discount' => number_format($cart->promotion_discount, 0, ',', '.').'₫',
            'cart_total' => number_format($cart->total, 0, ',', '.').'₫',
            'cartCount'    => $cart->items->count(),
        ];
    }

    public function mergeCartAfterLogin($customerId)
    {
        $sessionId = session()->get('cart_session_id');
        if (!$sessionId) return;
        $sessionCart = Cart::where('session_id', $sessionId)->first();
        if (!$sessionCart) return;
        $customerCart = Cart::firstOrCreate(['customer_id' => $customerId]);

        foreach ($sessionCart->items as $item) {
            $query = $customerCart->items()
                ->where('product_id', $item->product_id);

            if ($item->product_variant_id) {
                $query->where('product_variant_id', $item->product_variant_id);
            }

            $existing = $query->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->final_price = ($existing->price - $existing->discount) * $existing->quantity;
                $existing->save();
            } else {
                $item->cart_id = $customerCart->id;
                $item->save();
            }
        }

        $this->updateCartTotals($customerCart);

        $sessionCart->delete();
    }

}