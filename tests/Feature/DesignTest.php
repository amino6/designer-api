<?php

namespace Tests\Feature;

use App\Models\Design;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_all_designs(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        $this->getJson('/api/search/designs')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $design->id
            ]);
    }

    public function test_find_design_by_slug(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        $this->getJson('/api/designs/slug/' . $design->slug)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $design->id
            ]);
    }

    public function test_find_design_by_id_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        $this->actingAs($user)->getJson('/api/designs/' . $design->id)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $design->id
            ]);
    }

    public function test_upload_design(): void
    {
        $user = User::factory()->create();

        $image = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($user)->postJson('/api/designs', [
            'image' => $image,
        ])->json();

        Storage::disk($response['disk'])
            ->assertExists('uploads/designs/original/' . $response['image']);

        $this->assertDatabaseHas('designs', [
            'id' => $response['id']
        ]);
    }

    public function test_update_design(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        $description = fake()->words(random_int(22, 32), true);

        // without team
        $this->actingAs($user)->putJson('/api/designs/' . $design->id, [
            'title' => 'test title 2',
            'slug' => 'test-title-2',
            'description' => $description,
            'is_live' => false,
            'tags' => ['tag1']
        ])
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $design->id,
                'title' => 'test title 2',
                'slug' => 'test-title-2',
                'description' => $description,
                'is_live' => false,
                'tags_list' => [
                    'tag' => ['tag1'],
                    'tag_normalized' => ['tag1']
                ]
            ]);

        $this->assertDatabaseHas('designs', [
            'id' => $design->id,
            'title' => 'test title 2',
            'slug' => 'test-title-2',
            'description' => $description,
            'is_live' => false,
        ]);
    }

    public function test_restricted_update_design(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);
        $user2 = User::factory()->create();

        $description = fake()->words(random_int(22, 32), true);

        $this->actingAs($user2)->putJson('/api/designs/' . $design->id, [
            'title' => 'test title 2',
            'slug' => 'test-title-2',
            'description' => $description,
            'is_live' => false,
            'tags' => ['tag1']
        ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('designs', [
            'id' => $design->id,
            'title' => 'test title 2',
            'slug' => 'test-title-2',
            'description' => $description,
            'is_live' => false,
        ]);
    }

    public function test_delete_design(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        $this->actingAs($user)
            ->deleteJson('/api/designs/' . $design->id)
            ->assertSuccessful();
        
        $this->assertDatabaseMissing('designs', $design->toArray());
        $this->assertDatabaseCount('designs', 0);
    }

    public function test_restricted_delete_design(): void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);
        $user2 = User::factory()->create();

        $this->actingAs($user2)
            ->deleteJson('/api/designs/' . $design->id)
            ->assertStatus(403);
        
        $this->assertDatabaseHas('designs', [
            'id' => $design->id
        ]);
        $this->assertDatabaseCount('designs', 1);
    }

    public function test_like_unlike_design():void
    {
        $user = User::factory()->create();
        $design = $this->createDesign($user);

        // get the design and check the likes
        $design_likes = $this->getJson('/api/designs/slug/' . $design->slug)->json('data')['likes'];
        $this->assertEquals(0, $design_likes);

        // like the design
        $this->actingAs($user)
            ->post('/api/designs/' . $design->id . '/like')
            ->assertStatus(200);

        $design_likes = $this->getJson('/api/designs/slug/' . $design->slug)->json('data')['likes'];
        $this->assertEquals(1, $design_likes);
        $this->actingAs($user)
            ->postJson('/api/designs/' . $design->id . '/liked')
            ->assertJson([
                "liked" => true
            ]);

        // unlike the design
        $this->actingAs($user)
            ->postJson('/api/designs/' . $design->id . '/like')
            ->assertStatus(200);

        $design_likes = $this->getJson('/api/designs/slug/' . $design->slug)->json('data')['likes'];
        $this->assertEquals(0, $design_likes);
        $this->actingAs($user)
            ->postJson('/api/designs/' . $design->id . '/liked')
            ->assertJson([
                "liked" => false
            ]);
    }

    private function createDesign(User $user, ?Team $team = null): Design
    {
        return Design::create([
            'title' => 'test title',
            'slug' => 'test-title',
            'description' => fake()->words(random_int(22, 32), true),
            'is_live' => true,
            'upload_successful' => true,
            'team_id' => $team == null ? null : $team->id,
            'user_id' =>  $user->id,
            'image' => 'test_img.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTeam($owner_id = null): Team
    {
        if($owner_id == null) {
            return Team::factory()->create();
        }else {
            return Team::factory()->create([
                'owner_id' => $owner_id
            ]);
        }
    }
}
