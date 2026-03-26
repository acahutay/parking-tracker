<?php 

namespace App\Http\Controllers; 

use App\Models\ParkingSection; 
use App\Models\ParkingTicket; 
use Carbon\Carbon; 
use Illuminate\Http\Request; 

class ParkingController extends Controller 
{ 

	public function sections() 
    { 
        return ParkingSection::orderBy('floor')->orderBy('section_code')->get(); 
	} 

    public function availableSections() 
	{ 
        return ParkingSection::where('available_spaces', '>', 0)->get(); 
    } 

    public function seedSections() 
    { 
	    $sections = [ 
            ['floor' => '1', 'section_code' => 'A'], 
            ['floor' => '1', 'section_code' => 'B'], 
	        ['floor' => '2', 'section_code' => 'A'], 
            ['floor' => '2',  'section_code' => 'B'],  
        ]; 

        foreach ($sections as $section) { 
            ParkingSection::firstOrCreate( 
	            [ 
                    'floor' => $section['floor'], 
                    'section_code' => $section['section_code'],  
	            ], 
                [ 
                    'capacity' => 5, 
	                'available_spaces' => 5, 
                ] 
            ); 
	    } 

        return response()->json([ 
	        'message' => 'Sections seeded', 
            'sections' => ParkingSection::all(), 
        ]); 
	} 

    public function checkIn(Request $request) 
	{ 
        $request->validate([ 
            'plate_number' => 'required', 
	        'floor' => 'required', 
            'section_code' => 'required', 
        ]); 

        $section = ParkingSection::where('floor', $request->floor) 
            ->where('section_code', $request->section_code) 
	        ->first(); 

        if (! $section) { 
	        return response()->json([ 
                'message' => 'Section not found', 
            ], 404); 
	    } 

        if ($section->available_spaces <= 0) { 
	        return response()->json([ 
                'message' => 'Section is fully booked. Decline driver.',  
            ], 422); 
	    } 

        $ticket = ParkingTicket::create([ 
	        'parking_section_id' => $section->id, 
            'plate_number' => $request->plate_number, 
            'card_number' => 'CARD-'.strtoupper($section->floor.$section->section_code).'-'.rand(1000,  9999),  
	        'checked_in_at' => Carbon::now(), 
            'is_active' => true, 
        ]); 

        $section->available_spaces = $section->available_spaces - 1; 
        $section->save(); 

        return response()->json([ 
            'message' => 'Driver accepted. Give this parking card info.', 
	        'ticket' => $ticket, 
            'floor' => $section->floor, 
            'section' => $section->section_code, 
	        'available_spaces' => $section->available_spaces, 
        ]); 
    } 

    public function checkOut(Request $request) 
    { 
	    $request->validate([ 
            'ticket_id' => 'required', 
        ]); 

        $ticket  =  ParkingTicket::find($request->ticket_id); 

	    if (! $ticket || ! $ticket->is_active) { 
            return response()->json([ 
                'message' => 'Ticket not found or already checked out', 
	        ],  404); 
        } 

	    $section = ParkingSection::find($ticket->parking_section_id); 

        if ($section) { 

	        $section->available_spaces = $section->available_spaces + 1; 
            $section->save(); 
        } 

        $ticket->is_active  =  false; 
        $ticket->checked_out_at = Carbon::now(); 
	    $ticket->save(); 

        return response()->json([ 
	        'message' => 'Checkout complete', 
            'ticket' => $ticket, 
            'section' => $section, 
	    ]); 
    } 

	public function activeTickets() 
    { 
        return ParkingTicket::where('is_active', true) 
	        ->with('section') 
            ->orderByDesc('checked_in_at') 
            ->get(); 
	} 
} 
