<?php
namespace App\Http\Controllers;

use App\Exports\VentasExport;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteVentaController extends Controller
{
    /**
     * Generar el reporte de ventas en formato JSON.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reporteJson(Request $request)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $ventas = Venta::whereBetween('fecha_hora', [
            Carbon::parse($validated['fecha_inicio'])->startOfDay(),
            Carbon::parse($validated['fecha_fin'])->endOfDay(),
        ])
            ->with('detalles.producto') 
            ->get();

        // Mapear las ventas y agregar los detalles
        $ventas = $ventas->map(function ($venta) {
            return [
                'codigo'                 => $venta->codigo,
                'nombre_cliente'         => $venta->nombre_cl,
                'identificacion_cliente' => $venta->num_iden_cl,
                'correo_cliente'         => $venta->correo_cl,
                'cantidad_productos'     => $venta->detalles->sum('cantidad'),
                'monto_total'            => $venta->monto_total,
                'fecha_hora'             => \Carbon\Carbon::parse($venta->fecha_hora)->format('Y-m-d H:i:s'), 
                'detalles'               => $venta->detalles->map(function ($detalle) {
                    return [
                        'producto' => $detalle->producto->nombre, 
                        'cantidad' => $detalle->cantidad,
                        'precio'   => $detalle->producto->precio_unitario,
                    ];
                }),
            ];
        });

        $fechaActual = Carbon::now()->format('Y-m-d_H-i-s');

    
        return response()->json($ventas, 200)->setEncodingOptions(JSON_PRETTY_PRINT);
    }

    /**
     * Generar el reporte de ventas en formato XLSX.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function reporteXlsx(Request $request)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $ventas = Venta::whereBetween('fecha_hora', [
            Carbon::parse($validated['fecha_inicio'])->startOfDay(),
            Carbon::parse($validated['fecha_fin'])->endOfDay(),
        ])->get();

        $fechaActual = Carbon::now()->format('Y-m-d_H-i-s');

        return Excel::download(new VentasExport($ventas), "reporte_ventas_{$fechaActual}.xlsx");
    }
}
