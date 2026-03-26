<?php

namespace Tests\Feature;

use App\Models\ParkingSection;
use App\Models\ParkingTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guard_can_seed_sections(): void
    {
        $response = $this->postJson('/api/sections/seed');

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Sections seeded']);
        $this->assertGreaterThan(0, ParkingSection::count());
    }

    public function test_guard_can_check_in_and_section_count_goes_down(): void
    {
        $this->postJson('/api/sections/seed');

        $before = ParkingSection::where('floor', '1')->where('section_code', 'A')->first();

        $response = $this->postJson('/api/check-in', [
            'plate_number' => 'ABC-1001',
            'floor' => '1',
            'section_code' => 'A',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Driver accepted. Give this parking card info.']);

        $after = ParkingSection::where('floor', '1')->where('section_code', 'A')->first();
        $this->assertEquals($before->available_spaces - 1, $after->available_spaces);
    }

    public function test_guard_gets_decline_when_section_is_full(): void
    {
        $this->postJson('/api/sections/seed');

        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/check-in', [
                'plate_number' => 'FULL-'.$i,
                'floor' => '1',
                'section_code' => 'B',
            ]);
        }

        $response = $this->postJson('/api/check-in', [
            'plate_number' => 'FULL-6',
            'floor' => '1',
            'section_code' => 'B',
        ]);

        $response->assertStatus(422);
    }

    public function test_guard_can_check_out_and_space_is_returned(): void
    {
        $section = ParkingSection::create([
            'floor' => '3',
            'section_code' => 'C',
            'capacity' => 5,
            'available_spaces' => 4,
        ]);

        $ticket = ParkingTicket::create([
            'parking_section_id' => $section->id,
            'plate_number' => 'ZXC-777',
            'card_number' => 'CARD-3C-1111',
            'checked_in_at' => now(),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/check-out', [
            'ticket_id' => $ticket->id,
        ]);

        $response->assertStatus(200);

        $section->refresh();
        $ticket->refresh();

        $this->assertEquals(5, $section->available_spaces);
        $this->assertEquals(0, $ticket->is_active);
    }
}
