<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Model; 

class ParkingSection extends Model 
{ 
    protected $fillable  =  [ 
        'floor', 
	    'section_code', 
        'capacity', 
        'available_spaces',  
	]; 

    public function tickets(): HasMany 
	{ 
        return $this->hasMany(ParkingTicket::class); 
    } 
} 
