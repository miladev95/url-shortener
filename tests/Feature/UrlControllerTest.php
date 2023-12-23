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


    /**
     * test shorten url with authenticated user
     * @return void
     */
    public function test_shorten_url()
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

    /**
     * test convert short url to main url
     * @return void
     */
    public function test_convert_shortened_url()
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
                'data' => ['original_url' => $this->url],
            ]);

        $this->assertDatabaseHas('visits', [
            'url_id' => $url->id,
            'visitor_ip' => '127.0.0.1', // Assuming you run tests locally
        ]);
    }


    /**
     * test convert non exists url
     * @return void
     */
    public function test_convert_url_not_found()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/convert/abc1234');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Url not found',
            ]);
    }
}

