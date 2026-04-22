<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_access_protected_routes()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email']
            ]);

        $token = $response->json('access_token');

        // Test protected route
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJson(['email' => 'admin@example.com']);
    }

    public function test_project_crud_operations()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Create
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/projects', [
                'title' => 'Test Project',
                'description' => 'Test Description',
                'category' => 'Test Category',
                'image' => UploadedFile::fake()->create('project.jpg', 100)
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', ['title' => 'Test Project']);
        
        $projectId = $response->json('id');

        // Read (Public)
        $this->getJson('/api/projects')
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Test Project']);

        // Delete
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/projects/{$projectId}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
    }

    public function test_content_crud_operations()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Create
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/contents', [
                'title' => 'Test Content',
                'category' => 'Test Category',
                'image' => UploadedFile::fake()->create('content.jpg', 100)
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contents', ['title' => 'Test Content']);
        
        $contentId = $response->json('id');

        // Read (Public)
        $this->getJson('/api/contents')
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Test Content']);

        // Delete
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/contents/{$contentId}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('contents', ['id' => $contentId]);
    }

    public function test_marketing_endpoints()
    {
        // Subscribe
        $this->postJson('/api/subscribers', ['email' => 'test@example.com'])
            ->assertStatus(201);
        
        $this->assertDatabaseHas('subscribers', ['email' => 'test@example.com']);

        // Message
        $this->postJson('/api/messages', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello there!'
        ])->assertStatus(201);

        $this->assertDatabaseHas('messages', ['email' => 'john@example.com']);
    }
}
