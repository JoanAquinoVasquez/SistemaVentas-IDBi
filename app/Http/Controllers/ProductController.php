<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos.
     */
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json($products, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener productos', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Almacena un nuevo producto con transacción.
     */
    public function store(ProductStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->validated());

            DB::commit();
            return response()->json(['message' => 'Producto creado', 'product' => $product], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear el producto', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra un producto específico.
     */
    public function show(Product $product)
    {
        try {
            return response()->json($product, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Producto no encontrado', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Actualiza un producto con transacción.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            $product->update($request->validated());

            DB::commit();
            return response()->json(['message' => 'Producto actualizado', 'product' => $product], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo actualizar el producto', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un producto con transacción.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            $product->delete();
            DB::commit();
            return response()->json(['message' => 'Producto eliminado'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo eliminar el producto', 'message' => $e->getMessage()], 500);
        }
    }
}
