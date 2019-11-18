<?php


namespace App\Http\Forms;

use Illuminate\Contracts\Support\Responsable;
use App\{Profession, Skill, User};

class UserForm implements Responsable
{
    private $view;
    private $user;

    public function __construct($view, User $user)
    {
        $this->view = $view;
        $this->user = $user;
    }

    public function toResponse($request)
    {
        return view($this->view, [
            'user' => $this->user,
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
        ]);
    }
}