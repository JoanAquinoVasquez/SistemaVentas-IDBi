<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Http\Requests\DetalleVentaStoreRequest;
use App\Http\Requests\DetalleVentaUpdateRequest;

class DetalleVentaController extends Controller
{
    public function index()
    {
        return response()->json(DetalleVenta::with(['venta', 'product'])->get(), 200);
    }

    public function store(DetalleVentaStoreRequest $request)
    {
        $detalle = DetalleVenta::create($request->validated());
        return response()->json(['message' => 'Detalle de venta creado', 'detalle' => $detalle], 201);
    }

    public function show(DetalleVenta $detalleVenta)
    {
        return response()->json($detalleVenta->load(['venta', 'product']), 200);
    }

    public function update(DetalleVentaUpdateRequest $request, DetalleVenta $detalleVenta)
    {
        $detalleVenta->update($request->validated());
        return response()->json(['message' => 'Detalle de venta actualizado', 'detalle' => $detalleVenta], 200);
    }

    public function destroy(DetalleVenta $detalleVenta)
    {
        $detalleVenta->delete();
        return response()->json(['message' => 'Detalle de venta eliminado'], 200);
    }
}
