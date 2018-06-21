<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;

class UserController extends Controller
{
    protected $wechat_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->wechat_config = [
            'app_id' => 'wxff9c142e93044137',
            'secret' => '28c654c04c3ab0b5e7866f6016d6c6f5',

            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => storage_path('logs/wechat.log'),
            ],

            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/login'
            ]
        ];
    }

    public function profile()
    {
        $app = Factory::officialAccount($this->wechat_config);
        $oauth = $app->oauth;

        if ( ! file_exists(storage_path('app/wechat_user.json'))) {
            return $oauth->redirect();
        }

        $wechat_user = file_get_contents(storage_path('app/wechat_user.json'));

        return response()->json($wechat_user);
    }

    public function login()
    {
        $app = Factory::officialAccount($this->wechat_config);
        $oauth = $app->oauth;

        $user = $oauth->user();

        file_put_contents(storage_path('app/wechat_user.json'), json_encode($user));

        return redirect('profile');
    }
}
