<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBookingStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_booking_without_trim_error(): void
    {
        $user = User::factory()->create();

        $service = Service::query()->create([
            'name' => 'Wash',
            'slug' => 'wash-test',
            'description' => null,
            'price' => 5.00,
            'unit' => 'kg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $pickup = now()->addDay()->format('Y-m-d\TH:i');
        $delivery = now()->addDays(2)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->post(route('customer.bookings.store'), [
            'pickup_address' => '123 Pickup Street',
            'delivery_address' => '456 Delivery Road',
            'pickup_scheduled_at' => $pickup,
            'delivery_scheduled_at' => $delivery,
            'notes' => '',
            'items' => [(string) $service->id => '2'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $user->id,
        ]);
    }
}
