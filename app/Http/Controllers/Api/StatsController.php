<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $data = [
            'number_users' => User::count(),
            'number_posts' => Post::count(),
            'number_users_zero_posts' => User::has('posts', '=', 0)->count(),
        ];
        return apiResponse("success", $data);
    }
}
