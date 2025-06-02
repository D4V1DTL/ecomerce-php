<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $results = $cartItems->map(function ($cartItem) {
            return [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'product' => $cartItem->product,
            ];
        });
    
        return response()->json($results);
    }

    public function sincronizar(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        $user = $request->user();
        $results = [];
    
        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];
    
            $product = Product::findOrFail($productId);
    
            $cartItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();
    
            if ($cartItem) {
                if ($quantity > $product->stock) {
                    $cartItem->delete();
                    $results[] = [
                        'id' => $cartItem->id,
                        'quantity' => 0,
                        'product' => $product,
                        'message' => 'Cantidad solicitada excede el stock disponible. Producto eliminado del carrito.',
                        'status' => 'removed'
                    ];
                    continue;
                }
                $cartItem->quantity = $quantity;
                $cartItem->save();
                $results[] = [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'product' => $product,
                    'message' => 'Cantidad actualizada en el carrito',
                    'status' => 'updated'
                ];
            } else {
                if ($quantity > $product->stock) {
                    $results[] = [
                        'id' => null,
                        'quantity' => 0,
                        'product' => $product,
                        'message' => 'Cantidad solicitada excede el stock disponible',
                        'status' => 'error'
                    ];
                    continue;
                }
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
                $results[] = [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'product' => $product,
                    'message' => 'Producto agregado al carrito',
                    'status' => 'added'
                ];
            }
        }
    
        return response()->json([
            'results' => $results
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::findOrFail($productId);

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'id' => $cartItem->id,
            'quantity' => $cartItem->quantity,
            'product' => $product,
        ]);
    }

    // Nuevo método para actualizar cantidad directamente
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $quantity = $request->quantity;

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Producto no encontrado en el carrito'], 404);
        }

        $product = $cartItem->product;

        if ($quantity > $product->stock) {
            return response()->json(['error' => 'Cantidad solicitada excede el stock disponible'], 400);
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cantidad actualizada',
            'cart_item' => $cartItem->load('product'),
        ]);
    }

    public function remove(Request $request, $productId)
    {
        $user = $request->user();

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Producto no encontrado en el carrito'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Producto eliminado del carrito']);
    }

    public function clear(Request $request)
{
    $user = $request->user();

    CartItem::where('user_id', $user->id)->delete();

    return response()->json(['message' => 'Carrito vaciado exitosamente']);
}
}
