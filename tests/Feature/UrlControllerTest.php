<?php

// tests/Feature/UrlControllerTest.php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Url;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = 'https://example.com';
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function it_can_shorten_url()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/shorten', ['original_url' => $this->url]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'original_url',
                'short_url',
                'visit_count',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => $this->url,
        ]);
    }

    /** @test */
    public function it_can_convert_shortened_url()
    {
        $url = Url::factory()->create([
            'user_id' => $this->user->id,
            'original_url' => $this->url,
            'short_url' => 'abc123',
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/convert/abc123');

        $response->assertStatus(200)
            ->assertJson([
                'original_url' => $this->url,
            ]);

        $this->assertDatabaseHas('visits', [
            'url_id' => $url->id,
            'visitor_ip' => '127.0.0.1', // Assuming you run tests locally
        ]);
    }
}

