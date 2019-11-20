<?php

use App\Skill;
use App\Team;
use App\User;
use App\Profession;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    protected $professions;
    protected $skills;
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fetchRelations();

        $this->createAdmin();

        foreach (range(1, 499) as $i) {
            $this->createRandomUser();
        }
    }

    public function fetchRelations()
    {
        $this->professions = Profession::all();

        $this->skills = Skill::all();

        $this->teams = Team::all();
    }

    public function createAdmin()
    {
        $admin = factory(User::class)->create([
            'team_id' => $this->teams->firstWhere('name', 'Styde')->id,
            'name' => 'Cesar Acual',
            'email' => 'checha@mail.net',
            'password' => bcrypt('laravel'),
            'role' => 'admin',
            'created_at' => now(),
            'active' => true,
        ]);

        $admin->skills()->attach($this->skills);

        $admin->profile()->update([
            'bio' => 'Programador, profesor, editor, escritor, social media manager',
            'profession_id' => $this->professions->firstWhere('title', 'Desarrollador back-end')->id,
        ]);
    }

    public function createRandomUser()
    {
        $user = factory(User::class)->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id,
            'active' => rand(0, 3) ? true : false,
            'created_at' => now()->subDays(rand(1, 90)),
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        $user->profile->update([
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
    }
}
