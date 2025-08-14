<?php

use App\Models\User;
use App\Models\Group;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::first();
    $this->actingAs($this->user);
    Event::fake();
});

test('user can send message to group', function () {
    $group = Group::create([
        'name' => 'Test Group',
        'description' => 'Group for testing messages',
        'creator_id' => $this->user->id,
    ]);

    $group->users()->attach($this->user->id);

    $messageData = [
        'content' => 'Hello, this is a test message!',
    ];

    $response = $this->postJson("/api/message/{$group->id}", $messageData);

    $response->assertStatus(201);

    $this->assertDatabaseHas('messages', [
        'content' => $messageData['content'],
        'group_id' => $group->id,
        'sender_id' => $this->user->id,
    ]);

    // Vérifier que l'événement MessageSent a été déclenché
    Event::assertDispatched(MessageSent::class);
});
