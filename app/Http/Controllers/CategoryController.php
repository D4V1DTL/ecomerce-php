<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Listar todas las categorías
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Listado de categorías',
            'data' => $categories
        ]);
    }

    // Mostrar una categoría específica
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json([
            'message' => 'Detalle de la categoría',
            'data' => $category
        ]);
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data' => $category
        ], 201);
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data' => $category
        ]);
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}