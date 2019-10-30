<?php

return [
    'custom' => [
        'username' => [
            'required' => '用户名不能为空！',
            'unique' => '登录账号已存在！'
        ],
        'password' => [
            'required' => '密码不能为空！',
            'confirmed' => '两次密码不一致！',
            'min' => '密码最短6位！',
        ],
        'name' => [
            'required' => '请填写公司名称！'
        ],
        'tel' => [
            'required' => '请填写联系人电话！',
            'min' => '请填写正确的手机号码！',
            'max' => '请填写正确的手机号码！',
        ],
        'file' => [
            'required' => '请上传营业执照！'
        ],
        'title' => [
            'required' => '请填写标题！'
        ],
        'gys_price' => [
            'required' => '请填供货价！'
        ],
        'file' => [
            'required' => '请上传营业执照！'
        ],
        'hyzz' => [
            'required' => '请上传行业资质！'
        ],
        'hy_type' => [
            'required' => '请选择行业！'
        ],
        'hy_type_value' => [
            'required' => '请选择行业！'
        ],
        'activename' => [
            'required' => '请填写姓名！'
        ],
        'activetel' => [
            'required' => '请填写电话！',
            'min' => '请填写正确的手机号码！',
            'max' => '请填写正确的手机号码！',
        ],
    ],
];

