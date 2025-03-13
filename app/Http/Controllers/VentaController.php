<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use App\Http\Requests\VentaStoreRequest;
use App\Http\Requests\VentaUpdateRequest;

class VentaController extends Controller
{
    public function index()
    {
        return response()->json(Venta::with('detalles')->get(), 200);
    }

    public function store(VentaStoreRequest $request)
    {
        $venta = Venta::create($request->validated());
        return response()->json(['message' => 'Venta creada', 'venta' => $venta], 201);
    }

    public function show(Venta $venta)
    {
        return response()->json($venta->load('detalles'), 200);
    }

    public function update(VentaUpdateRequest $request, Venta $venta)
    {
        $venta->update($request->validated());
        return response()->json(['message' => 'Venta actualizada', 'venta' => $venta], 200);
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return response()->json(['message' => 'Venta eliminada'], 200);
    }
}
