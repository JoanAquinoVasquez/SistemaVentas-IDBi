<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');     // Referencia a la venta
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Referencia al producto
            $table->integer('cantidad');                                                   // Cantidad de este producto vendido
            $table->timestamps();                                                          // Fecha y hora de la venta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
