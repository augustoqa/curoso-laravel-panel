<?php

namespace Tests\Feature\Admin;

use App\{Profession, User, UserProfile};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'first_name' => 'Cesar',
        'last_name' => 'Acual',
        'email' => 'checha@gmail.com',
        'bio' => 'Programador Laravel y Vue.js',
        'twitter' => 'https://twitter.com/chechamas'
    ];

    /** @test */
    function a_user_can_edit_its_profile()
    {
        $user = factory(User::class)->create();

        $newProfession = factory(Profession::class)->create();

        //$this->actingAs($user);

        $response = $this->get('/editar-perfil/');

        $response->assertStatus(200);

        $response = $this->put('/editar-perfil/', [
            'first_name' => 'Cesar',
            'last_name' => 'Acual',
            'email' => 'checha@gmail.com',
            'bio' => 'Programador Laravel y Vue.js',
            'twitter' => 'https://twitter.com/chechamas',
            'profession_id' => $newProfession->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'first_name' => 'Cesar',
            'email' => 'checha@gmail.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador Laravel y Vue.js',
            'twitter' => 'https://twitter.com/chechamas',
            'profession_id' => $newProfession->id,
        ]);
    }

    /** @test */
    function the_user_cannot_change_its_role()
    {
        $user = factory(User::class)->create([
            'role' => 'user'
        ]);

        $response = $this->put('/editar-perfil/', $this->withData([
            'role' => 'admin',
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'user',
        ]);
    }

    /** @test */
    function the_user_cannot_change_its_password()
    {
        factory(User::class)->create([
            'password' => bcrypt('old123'),
        ]);

        $response = $this->put('/editar-perfil/', $this->withData([
            'email' => 'checha@gmail.com',
            'password' => 'new456'
        ]));

        $response->assertRedirect();

        $this->assertCredentials([
            'email' => 'checha@gmail.com',
            'password' => 'old123',
        ]);
    }
}
