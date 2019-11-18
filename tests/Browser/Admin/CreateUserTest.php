<?php

namespace Tests\Browser\Admin;

use App\{Profession, Skill, User};
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_be_created()
    {
        $profession = factory(Profession::class)->create();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->browse(function (Browser $browser) use ($profession, $skillA, $skillB) {
            $browser->visit('usuarios/nuevo')
                ->type('first_name', 'Cesar')
                ->type('last_name', 'Acual')
                ->type('email', 'checha@gmail.com')
                ->type('password', 'laravel')
                ->type('bio', 'Programador')
                ->select('profession_id', $profession->id)
                ->type('twitter', 'https://twitter.com/chechamas')
                ->check("skills[{$skillA->id}]")
                ->check("skills[{$skillB->id}]")
                ->radio('role', 'user')
                ->radio('state', 'active')
                ->press('Crear usuario')
                ->assertPathIs('/usuarios')
                ->assertSee('Cesar')
                ->assertSee('checha@gmail.com');
        });

        $this->assertCredentials([
            'first_name' => 'Cesar',
            'last_name' => 'Acual',
            'email' => 'checha@gmail.com',
            'password' => 'laravel',
            'role' => 'user',
            'active' => true,
        ]);

        $user = User::findByEmail('checha@gmail.com');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador',
            'twitter' => 'https://twitter.com/chechamas',
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id
        ]);
    }
}
