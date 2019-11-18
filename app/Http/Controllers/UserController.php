<?php

namespace App\Http\Controllers;

use App\{Skill, User};
use App\Http\Forms\UserForm;
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->byState(request('state'))
            ->byRole(request('role'))
            ->search(request('search'))
            ->orderByDesc('created_at')
            ->paginate();

        $users->appends(request(['search']));

        return view('users.index', [
            'users' => $users,
            'view' => 'index',
            'skills' => Skill::orderBy('name')->get(),
            'checkedSkills' => collect(request('skills')),
        ]);
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->paginate();

        return view('users.index', [
            'users' => $users,
            'view' => 'trash',
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return new UserForm('users.create', new User);
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return new UserForm('users.edit', $user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }

    function destroy($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $user->forceDelete();

        return redirect()->route('users.trashed');
    }
}
