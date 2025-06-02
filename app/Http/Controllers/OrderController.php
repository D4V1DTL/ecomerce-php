<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request)
{
    $user = $request->user();
    $orders = Order::where('user_id', $user->id)->with('products')->latest()->get();

    return response()->json([
        'orders' => $orders
    ]);
}

public function show(Request $request, $id)
{
    $user = $request->user();
    $order = Order::where('user_id', $user->id)->with('products')->findOrFail($id);

    return response()->json([
        'order' => $order->load('products')
    ]);
}

    public function store(Request $request)
    {
        $user = $request->user();

        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 400);
        }

        // Validar stock disponible
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return response()->json([
                    'message' => "No hay stock suficiente para el producto: {$item->product->name}"
                ], 400);
            }
        }

        $sub_total = 0;
        foreach ($cartItems as $item) {
            $sub_total += $item->product->price * $item->quantity;
        }
        $igv = $sub_total * 0.10;
        $total = $sub_total + $igv;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'sub_total' => $sub_total,
                'igv' => $igv,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->products()->attach($item->product->id, [
                    'quantity' => $item->quantity,
                    'price' => $item->product->discountPrice,
                ]);

                // Reducir stock del producto
                $item->product->decrement('stock', $item->quantity);
            }

            // Vaciar carrito
            $user->cartItems()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pedido creado correctamente',
                'order' => $order->load('products')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear el pedido', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,canceled',
        ]);

        $order = Order::findOrFail($orderId);

        // Opcional: validar que el usuario sea dueño o admin

        $order->status = $request->input('status');
        $order->save();

        return response()->json([
            'message' => 'Estado del pedido actualizado',
            'order' => $order,
        ]);
    }
}
