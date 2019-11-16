<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Forms\UserForm;
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};

class UserController extends Controller
{
    public function index()
    {
        if (request('search')) {
            $q = User::search(request('search'));
        } else {
            $q = User::query();
        }

        $users = $q->paginate()
            ->appends(request(['search']));

        $users->load('team');

        $title = 'Listado de usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->paginate();

        $title = 'Listado de usuarios en papelera';

        return view('users.index', compact('title', 'users'));
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
