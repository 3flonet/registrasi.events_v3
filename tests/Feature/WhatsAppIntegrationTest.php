<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Event;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_format_phone_numbers_correctly()
    {
        $service = new WhatsAppService();
        
        $this->assertEquals('62812345678', $service->formatPhoneNumber('0812345678'));
        $this->assertEquals('62812345678', $service->formatPhoneNumber('812345678'));
        $this->assertEquals('62812345678', $service->formatPhoneNumber('+62 812-3456-78'));
    }

    /** @test */
    public function it_can_send_a_whatsapp_message_via_service()
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true, 'target' => '62812345678'], 200),
        ]);

        config(['fonnte.token' => 'test-token']);

        $service = new WhatsAppService();
        $response = $service->sendMessage('0812345678', 'Test Message');

        $this->assertTrue($response['status']);
        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'test-token') &&
                   $request['target'] == '62812345678' &&
                   $request['message'] == 'Test Message';
        });
    }

    /** @test */
    public function it_handles_webhook_and_replies_with_ticket_link()
    {
        // Mock Fonnte response for the auto-reply
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        // Create mock registration
        $event = Event::factory()->create(['name' => 'Tech Conference 2026']);
        $registration = Registration::factory()->create([
            'event_id' => $event->id,
            'name' => 'John Doe',
            'uuid' => 'test-uuid-123',
            'phone_number' => '0812345678'
        ]);

        // Simulating Fonnte Webhook Request
        $payload = [
            'sender' => '62812345678',
            'message' => 'TICKET_' . $registration->uuid,
            'name' => 'John Doe'
        ];

        $response = $this->postJson('/api/whatsapp/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verify that a message was sent back
        Http::assertSent(function ($request) use ($registration) {
            return str_contains($request['message'], $registration->name) && 
                   str_contains($request['message'], $registration->uuid);
        });
    }

    /** @test */
    public function it_handles_invalid_ticket_id_gracefully()
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $payload = [
            'sender' => '62812345678',
            'message' => 'TICKET_invalid-uuid',
        ];

        $response = $this->postJson('/api/whatsapp/webhook', $payload);

        $response->assertStatus(200);
        
        Http::assertSent(function ($request) {
            return str_contains($request['message'], 'tidak ditemukan');
        });
    }
}
