<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function authorization()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
            ],
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/catalog');
        }

        return back()->withErrors(['email' => 'Неверные данные входа']);
    }

    public function registration()
    {
        return view('auth.register');
    }

   public function register(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'max:100', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s]+$/u'],
        'email' => [
            'required',
            'email',
            'max:150',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'unique:users,email', // 🔥 ВАЖНО
        ],
        'password' => ['required', 'string', 'confirmed', 'min:6'],
    ], [
        'name.required' => 'Введите имя',
        'name.regex' => 'Имя может содержать только буквы и пробелы.',
        'email.required' => 'Введите email',
        'email.email' => 'Введите корректный email',
        'email.regex' => 'Email содержит недопустимые символы',
        'email.unique' => 'Пользователь с таким email уже существует', // 🔥 ТЕКСТ ОШИБКИ
        'password.required' => 'Введите пароль',
        'password.confirmed' => 'Пароли не совпадают',
        'password.min' => 'Пароль должен быть не меньше 6 символов',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'user',
    ]);

    Auth::login($user);

    return redirect('/profile');
}



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/catalog');
    }
}
