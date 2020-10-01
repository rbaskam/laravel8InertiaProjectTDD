<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseMigrations;
    
    protected $user;
    /**
     * Whats Run on each Instance
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Team::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );
    }
    
    /**
     * Check for the Index page availabiltiy when not logged in
     *
     * @return void
     */
    public function testViewPostsPageIsNotAccessibleWhenNotLoggedIn()
    {
        $response = $this->get('/posts');

        $response->assertStatus(302);
    }

    /**
     * Check for the Index page availabiltiy when logged in
     *
     * @return void
     */
    public function testViewPostsPageIsNotAccessibleWhenLoggedIn()
    {
        $response = $this->actingAs($this->user)->get('/posts');

        $response->assertStatus(200);
    }

    /**
     * Check for the Index page post
     *
     * @return void
     */
    public function testViewPostsOnTheIndexPage()
    {
        $post = Post::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );
        
        $response = $this->actingAs($this->user)->get('/posts');

        $response->assertSuccessful();
        $response->assertSee($post->title);
        $response->assertSee($post->body);
    }

    /**
     * See if a not logged in user can see a post
     *
     * @return void
     */
    public function testUserNotLoggedInCanViewSinglePost()
    {
        $post = Post::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );
        
        $response = $this->get('/posts/' . $post->id);

        $response->assertStatus(302);
    }

    /**
     * See if logged in user can see a post
     *
     * @return void
     */
    public function testUserCanViewSinglePost()
    {
        $post = Post::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );
        
        $response = $this->actingAs($this->user)->get('/posts/' . $post->id);
        $response->assertSuccessful();
        $response->assertSee($post->title);
        $response->assertSee($post->body);
    }

    /**
     * See if a not logged in user can create a post
     *
     * @return void
     */
    public function testUserNotLoggedInCantCreateAPost()
    {
        $post = Post::factory()->make(
            [
                'user_id' => $this->user->id
            ]
        );
        
        //When user submits post request to create post endpoint
        $response = $this->post('/posts', $post->toArray());
        $response->assertStatus(302);
    }

    /**
     * See if a not logged in user can create a post
     *
     * @return void
     */
    public function testUserLoggedInCanCreateAPost()
    {
        $post = Post::factory()->make(
            [
                'user_id' => $this->user->id
            ]
        );
        
        $response = $this->actingAs($this->user)->post('/posts', $post->toArray());
        
        $response->assertStatus(201);
        $this->assertEquals(1, Post::all()->count());
    }

    /**
     * See if a not logged in user can see the create a post
     *
     * @return void
     */
    public function testUserNotLoggedInCantSeeTheCreateAPostPage()
    {   
        $response = $this->get('/posts/create');
        
        $response->assertStatus(302);
    }

    /**
     * See if a logged in user can see the create a post
     *
     * @return void
     */
    public function testUserLoggedInCanSeeTheCreateAPostPage()
    {
        $response = $this->actingAs($this->user)->get('/posts/create');
        $response->assertStatus(200);
    }

    
}
