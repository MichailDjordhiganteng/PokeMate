<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('trainer.profile', ['user' => $request->user()->load('trainerProfile')]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:2000'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $oldAvatar = $user->trainerProfile?->avatar;
        $avatar = $request->hasFile('avatar') ? $request->file('avatar')->store('avatars', 'public') : null;
        try {
            DB::transaction(function () use ($user, $data, $avatar) {
                $user->update(collect($data)->only(['full_name', 'email'])->all());
                if (! empty($data['password'])) {
                    $user->update(['password' => $data['password']]);
                }
                $profile = collect($data)->only(['phone', 'address'])->all();
                if ($avatar !== null) {
                    $profile['avatar'] = $avatar;
                }
                $user->trainerProfile()->updateOrCreate([], $profile);
            });
        } catch (\Throwable $e) {
            if ($avatar !== null) {
                Storage::disk('public')->delete($avatar);
            }
            throw $e;
        }
        if ($avatar !== null && $oldAvatar !== null) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
