<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test listing all chat history for the authenticated user.
     */
    public function test_can_list_chats()
    {
        Chat::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/chats');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /**
     * Test creating a chat with AI streaming response.
     */
    public function test_can_create_chat()
    {
        $payload = ['user_message' => 'Tell me about renewable energy.'];

        $response = $this->actingAs($this->user)->postJson('/api/chats', $payload);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
    }

    /**
     * Test retrieving a single chat by ID.
     */
    public function test_can_retrieve_chat()
    {
        $chat = Chat::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/chats/{$chat->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $chat->id,
                'user_message' => $chat->user_message,
            ]);
    }

    /**
     * Test updating a chat with new user_message and AI streaming response.
     */
    public function test_can_update_chat()
    {
        $chat = Chat::factory()->create(['user_id' => $this->user->id]);
        $payload = ['user_message' => 'Update with more details on solar energy.'];

        $response = $this->actingAs($this->user)->putJson("/api/chats/{$chat->id}", $payload);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
    }

    /**
     * Test deleting a chat.
     */
    public function test_can_delete_chat()
    {
        $chat = Chat::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/chats/{$chat->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('chats', ['id' => $chat->id]);
    }

    /**
     * Test unauthorized access to chats.
     */
    public function test_unauthorized_access_is_blocked()
    {
        $response = $this->getJson('/api/chats');

        $response->assertStatus(401);
    }
}
