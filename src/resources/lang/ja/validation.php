<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'required' => ':attributeを入力してください',
    'email' => ':attribute は正しいメールアドレス形式で入力してください。',
    'string' => ':attribute は文字列で入力してください。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'confirmed' => ':attribute が一致しません。',
    'unique' => 'この :attribute はすでに使用されています。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'ユーザー名',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認用）',
        'postal_code' => '郵便番号',
        'address' => '住所',
        'building' => '建物名',
        'image' => 'プロフィール画像',
    ],
];