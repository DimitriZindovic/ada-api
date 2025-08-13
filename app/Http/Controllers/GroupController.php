<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $groups = Group::with(['users'])
            ->whereHas('users', function ($query) {
            $query->where('users.id', auth()->user()->id);
            })
            ->get();

        return response()->json($groups);
    }

    public function show(Request $request, Group $group): JsonResponse
    {
        $group->load(['users']);
        return response()->json($group);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ]);

        $userIds = collect($validated['users'])
            ->push(auth()->id())
            ->unique()
            ->values();

        $group = new Group($validated);
        $group->save();

        $group->users()->attach(
            $userIds->mapWithKeys(fn($id) => [$id => ['joined_at' => now()]])
        );

        return response()->json($group->load('users'), 201);
    }

    public function update(Request $request, Group $group): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'sometimes|array|min:1',
            'users.*' => 'exists:users,id',
        ]);

        $group->fill(['name' => $validated['name']]);
        $group->save();

        if (isset($validated['users'])) {
            $userIds = collect($validated['users'])
                ->push(auth()->id())
                ->unique()
                ->values();

            $syncData = $userIds->mapWithKeys(fn($id) => [$id => ['joined_at' => now()]]);
            $group->users()->sync($syncData);
        }

        return response()->json($group, 200);
    }

    public function leave(Request $request, Group $group): JsonResponse
    {
        $user = auth()->user();

        $group->users()->detach($user->id);

        return response()->json();
    }
}
