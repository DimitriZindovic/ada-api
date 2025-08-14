<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Group;
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

    public function store(Request $request, Group $group): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = new Message($validated);
        $message->group()->associate($group);
        $message->sender()->associate(auth()->user());
        $message->save();

        $message->load(['sender', 'group']);

        broadcast(new MessageSent($message));

        return response()->json($message, 201);
    }
}
