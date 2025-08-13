<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function index($group): JsonResponse
    {
        $messages = Message::with(['sender', 'group'])
            ->where('group_id', $group)
            ->get();

        return response()->json($messages);
    }
}
