<?php

namespace Tests\Unit;

use App\Topic;
use App\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    private function create_user(){
        $this->user = factory(User::class)->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('password')
        ]);
    }

    public function test_user_register()
    {
        $response = $this->post('/api/register', [
           'name' => 'user',
           'email' => 'user@user.com',
           'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_user_register_incorrect_email()
    {
        $response = $this->post('/api/register', [
           'name' => 'user',
           'email' => 'user',
           'password' => 'password'
        ]);

        $response->assertStatus(302);
    }

    public function test_user_login()
    {
        $this->create_user();

        $response = $this->post('/api/login', [
            'email' => 'user@user.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    public function test_user_login_incorrect(){
        $this->create_user();

        $response = $this->post('/api/login', [
            'email' => 'user@user.com',
            'password' => 'bad password'
        ]);

        $response->assertStatus(422);
    }

    public function test_get_user()
    {
        $this->create_user();

        $response = $this->actingAs($this->user)->get('/api/user');
        $response->assertStatus(200);
    }

    public function test_user_logout(){
        $this->create_user();

        $result = json_decode($this->json('POST','/api/login', [
            'email' => 'user@user.com',
            'password' => 'password'
        ])->getContent(), true);
        $result = $this->json('POST','/api/logout/', [], ['Authentication' => "'Bearer {$result['meta']['token']}"]);
        $result->assertStatus(200);
    }

    public function test_create_topic(){
        $this->create_user();

        $response = $this->actingAs($this->user)->post('/api/topics', [
            'title' => 'Test title',
            'body' => 'Test body',
        ]);

        $response->assertStatus(201);
    }

    public function test_create_topic_empty(){
        $this->create_user();

        $response = $this->actingAs($this->user)->json('POST','/api/topics', [
        ]);
        $response->assertStatus(422);
    }

    public function test_update_topic(){
        $this->create_user();

        /** @var Topic $topic */
        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->json('PATCH', '/api/topics/'. $topic->id, [
            'title' => 'updated title',
            'body' => 'updated body',
        ]);

        $response->assertStatus(200);
    }

    public function test_delete_topic(){
        $this->create_user();
        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete('/api/topics/'. $topic->id);

        $response->assertStatus(204);
    }

    public function test_delete_topic_unauthorized(){
        $this->create_user();
        $topic = factory(Topic::class)->create();

        $response = $this->actingAs($this->user)->delete('/api/topics/'. $topic->id);

        $response->assertStatus(403);
    }

    public function test_create_post(){
        $this->create_user();

        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->post('/api/topics/'. $topic->id .'/posts', [
            'body' => 'new body'
        ]);

        $response->assertStatus(201);
    }

    public function test_create_post_empty(){
        $this->create_user();

        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->post('/api/topics/'. $topic->id .'/posts', [
        ]);

        $response->assertStatus(302);
    }

    public function test_update_post(){
        $this->create_user();

        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $post = factory(Post::class)->create(['user_id' => $this->user->id, 'topic_id' => $topic->id]);

        $response = $this->actingAs($this->user)->json('PATCH', '/api/topics/'. $topic->id . '/posts/' . $post->id, [
            'body' => 'updated new body',
        ]);

        $response->assertStatus(200);
    }

    public function test_update_post_unauthorized(){
        $this->create_user();

        $topic = factory(Topic::class)->create(['user_id' => $this->user->id]);

        $post = factory(Post::class)->create(['topic_id' => $topic->id]);

        $response = $this->actingAs($this->user)->json('PATCH', '/api/topics/'. $topic->id . '/posts/' . $post->id, [
            'body' => 'updated new body',
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_post(){
        $this->create_user();
        $post = factory(Post::class)->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->delete('/api/topics/'. $post->topic_id . '/posts/'. $post->id);

        $response->assertStatus(204);
    }

    public function test_delete_post_unauthorized(){
        $this->create_user();
        $post = factory(Post::class)->create(['user_id' => $this->user->id+1]);
        $response = $this->actingAs($this->user)->delete('/api/topics/'. $post->topic_id . '/posts/'. $post->id);

        $response->assertStatus(403);
    }

    public function test_like_post(){
        $this->create_user();
        $post = factory(Post::class)->create(['user_id' => $this->user->id + 1]);
        $response = $this->actingAs($this->user)->post('/api/topics/'. $post->topic_id . '/posts/'. $post->id . '/likes');
        $response->assertStatus(204);
    }

    public function test_like_own_post(){
        $this->create_user();
        $post = factory(Post::class)->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->post('/api/topics/'. $post->topic_id . '/posts/'. $post->id . '/likes');
        $response->assertStatus(403);
    }
}
