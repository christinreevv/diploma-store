<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('id', $search);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users', 'search'));
    }
public function show(User $user)
{
    $user->load([
        'orders.items',
    ]);

    return view('admin.users.show', compact('user'));
}
}
