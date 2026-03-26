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
        Schema::create('parking_tickets', function (Blueprint $table) { 
            $table->id(); 
            $table->foreignId('parking_section_id')->constrained('parking_sections'); 
            $table->string('plate_number'); 
            $table->string('card_number'); 
            $table->timestamp('checked_in_at'); 
            $table->timestamp('checked_out_at')->nullable(); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps(); 
        }); 
    } 

    /** 
     * Reverse the migrations. 
     */ 
    public function down(): void 
    { 
        Schema::dropIfExists('parking_tickets'); 
    } 
}; 
