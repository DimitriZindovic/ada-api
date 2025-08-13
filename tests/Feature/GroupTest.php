<?php

use App\Models\User;
use App\Models\Group;

beforeEach(function () {
    $this->user = User::first();
    $this->actingAs($this->user);
});

test('user can create a group', function () {
    $user = User::skip(1)->first();

    $groupData = [
        'name' => 'Test Group',
        'phones' => [$user->phone],
    ];

    $response = $this->postJson('/api/groups', $groupData);

    $response->assertStatus(201);

    $this->assertDatabaseHas('groups', [
        'name' => $groupData['name'],
    ]);
});

test('user can create a group with emails', function () {
    $user = User::skip(1)->first();

    $groupData = [
        'name' => 'Email Test Group',
        'emails' => [$user->email],
    ];

    $response = $this->postJson('/api/groups', $groupData);

    $response->assertStatus(201);

    $this->assertDatabaseHas('groups', [
        'name' => $groupData['name'],
    ]);
});

test('user can list their groups', function () {
    $response = $this->getJson('/api/groups');
    $response->assertStatus(200);
});

test('user can show a specific group', function () {
    $group = Group::first();
    $response = $this->getJson("/api/groups/{$group->id}");
    $response->assertStatus(200);
});

test('authenticated user can update a group', function () {
    $otherUser = User::skip(1)->first();
    $group = Group::first();

    $updateData = [
        'name' => 'Updated Group Name',
        'emails' => [$otherUser->email],
    ];

    $response = $this->putJson("/api/groups/{$group->id}", $updateData);

    $response->assertStatus(200);
});

test('user can leave a group', function () {
    $group = Group::first();
    $response = $this->putJson("/api/group/{$group->id}/leave");
    $response->assertStatus(200);
});
