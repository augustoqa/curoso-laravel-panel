<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Skill, Sortable, User, UserFilter};
use App\Http\Forms\UserForm;
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};

class UserController extends Controller
{
    public function index(Request $request, UserFilter $filters, Sortable $sortable)
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->onlyTrashedIf($request->routeIs('users.trashed'))
            ->filterBy($filters, $request->only(['state', 'role', 'search', 'skills', 'from', 'to', 'order']))
            ->orderByDesc('created_at')
            ->paginate();

        $users->appends($filters->valid());

        $sortable->appends($filters->valid());

        return view('users.index', [
            'view' => $request->routeIs('users.trashed') ? 'trash' : 'index',
            'users' => $users,
            'skills' => Skill::orderBy('name')->get(),
            'checkedSkills' => collect(request('skills')),
            'sortable' => $sortable,
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
