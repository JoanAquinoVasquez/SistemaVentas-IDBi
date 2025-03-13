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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();                 // Código de la venta
            $table->string('nombre_cl');                        // Nombre del cliente
            $table->string('num_iden_cl');                      // Identificación del cliente (DNI o RUC)
            $table->string('correo_cl')->nullable();            // Correo del cliente
            $table->foreignId('user_id')->constrained('users'); // Vendedor (usuario que realizó la venta)
            $table->decimal('monto_total', 10, 2);              // Monto total de la venta
            $table->timestamp('fecha_hora');                    // Fecha y hora de la venta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
