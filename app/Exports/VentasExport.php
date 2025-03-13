<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

class VentasExport implements FromCollection, WithStyles, WithHeadings, WithEvents
{
    protected $ventas;

    public function __construct($ventas)
    {
        $this->ventas = $ventas;
    }

    public function collection()
    {
        return $this->ventas->map(function ($venta) {
            return [
                'Código'                 => $venta->codigo,
                'Nombre cliente'         => $venta->nombre_cl,
                'Identificación cliente' => $venta->num_iden_cl,
                'Correo cliente'         => $venta->correo_cl,
                'Cantidad productos'     => $venta->detalles->sum('cantidad'),
                'Monto total'            => $venta->monto_total,
                'Fecha y hora'           => \Carbon\Carbon::parse($venta->fecha_hora)->format('Y-m-d H:i:s'), // Asegúrate de convertir a Carbon aquí
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre cliente',
            'Identificación cliente',
            'Correo cliente',
            'Cantidad productos',
            'Monto total',
            'Fecha y hora',
        ];
    }

    public function styles($sheet)
    {
        return [
            1   => [
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCC00']], // Color de fondo amarillo
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ],
            'A' => [
                'alignment' => ['horizontal' => 'left'],
            ],
            'B' => [
                'alignment' => ['horizontal' => 'left'],
            ],
            'C' => [
                'alignment' => ['horizontal' => 'center'],
            ],
            'D' => [
                'alignment' => ['horizontal' => 'left'],
            ],
            'E' => [
                'alignment' => ['horizontal' => 'center'],
            ],
            'F' => [
                'alignment' => ['horizontal' => 'right'],
            ],
            'G' => [
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Hacer los encabezados en negrita y con color de fondo amarillo
                $event->sheet->getStyle('A1:G1')->getFont()->setBold(true);
                $event->sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $event->sheet->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('FFCC00');
                $event->sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

                                                                                                                        // Ajustar el ancho de las columnas automáticamente al contenido
                                                                                                                        // Ahora recorremos las columnas por su índice numérico
                $highestColumn      = $event->sheet->getDelegate()->getHighestColumn();                                 // Devuelve la última columna (por ejemplo, 'G')
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // Convierte la columna a índice numérico

                // Ajustar automáticamente el ancho de las columnas
                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }

}
