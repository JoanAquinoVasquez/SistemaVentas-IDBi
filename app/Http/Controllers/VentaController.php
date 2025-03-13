<?php
namespace App\Http\Controllers;

use App\Http\Requests\VentaStoreRequest;
use App\Http\Requests\VentaUpdateRequest;
use App\Models\DetalleVenta;
use App\Models\Product;
use App\Models\Venta;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        try {
            $ventas = Venta::with('detalles.producto')->get();
            return response()->json($ventas, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener ventas', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(VentaStoreRequest $request)
    {
        DB::beginTransaction(); 

        try {

            $monto_total = 0;

            if (! $request->has('detalles') || empty($request->detalles)) {
                return response()->json(['error' => 'Debe agregar al menos un producto a la venta.'], 400);
            }

            foreach ($request->detalles as $detalle) {
                $producto = Product::findOrFail($detalle['product_id']);

                if ($producto->stock < $detalle['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto {$producto->nombre}. Disponibles: {$producto->stock}");
                }

                $monto_total += $producto->precio_unitario * $detalle['cantidad'];

                $producto->stock -= $detalle['cantidad'];
                $producto->save();
            }

            $venta = Venta::create([
                'codigo'      => $request->codigo,
                'nombre_cl'   => $request->nombre_cl,
                'num_iden_cl' => $request->num_iden_cl,
                'correo_cl'   => $request->correo_cl,
                'user_id'     => $request->user_id,
                'fecha_hora'  => $request->fecha_hora,
                'monto_total' => round($monto_total, 2),
            ]);

            foreach ($request->detalles as $detalle) {
                DetalleVenta::create([
                    'venta_id'   => $venta->id,
                    'product_id' => $detalle['product_id'],
                    'cantidad'   => $detalle['cantidad'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Venta creada correctamente',
                'venta'   => $venta->load('detalles.producto'),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la venta', 'message' => $e->getMessage()], 400);
        }
    }

    public function show(Venta $venta)
    {
        try {
            return response()->json($venta->load('detalles'), 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener la venta', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(VentaUpdateRequest $request, Venta $venta)
    {
        DB::beginTransaction();
        Log::info($request->all());

        try {
            $validated = $request->validated();

            $monto_total = 0;

            if ($request->has('detalles')) {
                $detallesActuales = $venta->detalles->keyBy('product_id');

                foreach ($request->detalles as $detalle) {
                    $producto = Product::findOrFail($detalle['product_id']);

                    $cantidadNueva    = $detalle['cantidad'];
                    $cantidadAnterior = $detallesActuales[$detalle['product_id']]->cantidad ?? 0;
                    $diferenciaStock  = $cantidadNueva - $cantidadAnterior;

                    if ($producto->stock < $diferenciaStock) {
                        throw new Exception("Stock insuficiente para el producto {$producto->nombre}. Disponibles: {$producto->stock}");
                    }

                    $producto->stock -= $diferenciaStock;
                    $producto->save();

                    $monto_total += $producto->precio_unitario * $cantidadNueva;
                }
            }

            $venta->update([
                'codigo'      => $validated['codigo'] ?? $venta->codigo,
                'nombre_cl'   => $validated['nombre_cl'] ?? $venta->nombre_cl,
                'num_iden_cl' => $validated['num_iden_cl'] ?? $venta->num_iden_cl,
                'correo_cl'   => $validated['correo_cl'] ?? $venta->correo_cl,
                'user_id'     => $validated['user_id'] ?? $venta->user_id,
                'fecha_hora'  => $validated['fecha_hora'] ?? $venta->fecha_hora,
                'monto_total' => $monto_total > 0 ? round($monto_total, 2) : $venta->monto_total,
            ]);

            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalle) {
                    $detalleVenta = DetalleVenta::where('venta_id', $venta->id)
                        ->where('product_id', $detalle['product_id'])
                        ->first();

                    if ($detalleVenta) {
                        if ($detalle['cantidad'] > 0) {
                            $detalleVenta->update(['cantidad' => $detalle['cantidad']]);
                        } else {
                            $detalleVenta->delete();
                        }
                    } else {
                        DetalleVenta::create([
                            'venta_id'   => $venta->id,
                            'product_id' => $detalle['product_id'],
                            'cantidad'   => $detalle['cantidad'],
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Venta actualizada correctamente',
                'venta'   => $venta->load('detalles.producto'),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Error al actualizar la venta',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();
            return response()->json(['message' => 'Venta eliminada'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la venta', 'message' => $e->getMessage()], 500);
        }
    }
}
