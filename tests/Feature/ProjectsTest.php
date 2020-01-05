<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */

    public function only_authenticated_users_can_create_projects()
    {
        $attributes = factory ('App\Project')->raw(['owner_id' => null]);

        $this->post('/projects', $attributes)->assertRedirect('login');

    }

    /** @test */

    public function a_user_can_create_a_project()
    {
        // sign a user in for the test
        $this->actingAs(factory('App\User')->create());

        $this->withoutExceptionHandling();

        $attributes = [
            'title' => $this->faker->sentence
            , 'description' => $this->faker->paragraph
        ];

        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    /** @test */

    public function a_user_can_view_a_project()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */

    public function a_project_requires_a_title()
    {
        // Sign in a user
        $this->actingAs(factory('App\User')->create());

        // create - build out an attribute and save to db as an object,
        // make will build it , but not save it as an object,
        // raw build out attribute, but save it as an array

        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */

    public function a_project_requires_a_description()
    {
        // Sign in a user
        $this->actingAs(factory('App\User')->create());

        // create - build out an attribute and save to db as an object,
        // make will build it , but not save it as an object,
        // raw build out attribute, but save it as an array

        $attributes = factory('App\Project')->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }


}
