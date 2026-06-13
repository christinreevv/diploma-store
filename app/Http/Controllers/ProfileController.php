<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        // Загружаем пользователя с его заказами и товарами внутри заказов
        $user = Auth::user()->load(['orders.items.product']);

        return view('user.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();

        return view('user.edit', compact('user'));
    }

    public function update(Request $request)
{
    $user = Auth::user();

    // Валидация всех полей
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zА-Яа-яЁё\s-]+$/u'],
        'email' => [
            'required',
            'email:rfc,dns',
            Rule::unique('users')->ignore($user->id),
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ],
        'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
        'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ], [
        'name.regex' => 'Имя может содержать только буквы, пробелы и дефисы.',
        'email.regex' => 'Некорректный формат электронной почты.',
    ]);

    // Обновляем имя и email
    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
    ]);

    // Если задан новый пароль — обновляем
    if (!empty($validated['new_password'])) {
        $user->password = Hash::make($validated['new_password']);
        $user->save();
    }

    return redirect()->route('profile.show')->with('success', 'Профиль успешно обновлён.');
}

public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048', // макс 2 МБ
    ]);

    $user = auth()->user();

    // Удаляем старый аватар, если он есть
    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Сохраняем новый файл
    $path = $request->file('avatar')->store('avatars', 'public');

    $user->avatar = $path;
    $user->save();

    return back()->with('success', 'Аватар обновлен!');
}



}
