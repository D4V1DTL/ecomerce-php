<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{   
    // Listado de productos con paginación
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Product::with('category');
    
        // Filtro por categorías
        if ($request->has('category_id')) {
            $categoryIds = $request->get('category_id');
            if (is_string($categoryIds)) {
                $categoryIds = explode(',', $categoryIds);
            }
            $query->whereIn('category_id', (array)$categoryIds);
        }

        // Filtro por búsqueda en nombre o descripción
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filtro por precio mínimo
        if ($request->has('minPrice')) {
            $query->where('price', '>=', $request->get('minPrice'));
        }

        // Filtro por precio máximo
        if ($request->has('maxPrice')) {
            $query->where('price', '<=', $request->get('maxPrice'));
        }

        // Ordenamiento avanzado
        if ($request->has('sortBy')) {
            switch ($request->get('sortBy')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    // Puedes agregar un orden por defecto si lo deseas
                    break;
            }
        }
    
        $products = $query->paginate($perPage);
    
        return response()->json([
            'message' => 'Listado de productos',
            'data' => collect($products->items())->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'rating' => $product->rating,
                    'discountPrice' => $product->discountPrice,
                    'featured' => $product->featured,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category ? $product->category->name : null,
                ];
            }),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

        // Detalle de un producto
        public function show($id)
        {
            $product = Product::with('category')->findOrFail($id);
            return response()->json([
                'message' => 'Detalle del producto',
                'data' => $product
            ]);
        }
    
        // Crear un producto
        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'image' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
            ]);
    
            $product = Product::create($validated);
    
            return response()->json([
                'message' => 'Producto creado exitosamente',
                'data' => $product
            ], 201);
        }
    
        // Actualizar un producto
        public function update(Request $request, $id)
        {
            $product = Product::findOrFail($id);
    
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric',
                'stock' => 'sometimes|required|integer',
                'image' => 'nullable|string',
                'category_id' => 'sometimes|required|exists:categories,id',
            ]);
    
            $product->update($validated);
    
            return response()->json([
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
            ]);
        }
    
        // Eliminar un producto
        public function destroy($id)
        {
            $product = Product::findOrFail($id);
            $product->delete();
    
            return response()->json([
                'message' => 'Producto eliminado exitosamente'
            ]);
        }
}
