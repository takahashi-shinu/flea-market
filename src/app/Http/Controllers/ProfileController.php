<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 編集画面表示
    public function edit()
    {
        $user = auth()->user();

        return view('users.profile', compact('user'));
    }

    // 更新処理
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        // 画像がアップロードされた場合
        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }

        $user->update($data);

        return redirect()->route('mypage')
            ->with('success', 'プロフィールを更新しました');
    }
}
