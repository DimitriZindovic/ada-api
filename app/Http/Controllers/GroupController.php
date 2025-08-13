<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
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
            'name' => 'sometimes|string|max:255',
            'phones' => 'sometimes|array|min:1',
            'phones.*' => 'string|exists:users,phone',
            'emails' => 'sometimes|array|min:1',
            'emails.*' => 'string|email|exists:users,email',
        ]);

        $userIds = collect();

        if (!empty($validated['phones'])) {
            $userIds = $userIds->merge(
                User::whereIn('phone', $validated['phones'])->pluck('id')
            );
        }

        if (!empty($validated['emails'])) {
            $userIds = $userIds->merge(
                User::whereIn('email', $validated['emails'])->pluck('id')
            );
        }

        $userIds = $userIds->push(auth()->id())->unique()->values();

        $group = new Group(['name' => $validated['name'] ?? null]);
        $group->save();

        $group->users()->attach(
            $userIds->mapWithKeys(fn($id) => [$id => ['joined_at' => now()]])
        );

        return response()->json('', 201);
    }

    public function update(Request $request, Group $group): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phones' => 'sometimes|array|min:1',
            'phones.*' => 'string|exists:users,phone',
            'emails' => 'sometimes|array|min:1',
            'emails.*' => 'string|email|exists:users,email',
        ]);

        $userIds = collect();

        if (!empty($validated['phones'])) {
            $userIds = $userIds->merge(
                User::whereIn('phone', $validated['phones'])->pluck('id')
            );
        }

        if (!empty($validated['emails'])) {
            $userIds = $userIds->merge(
                User::whereIn('email', $validated['emails'])->pluck('id')
            );
        }

        $userIds = $userIds->push(auth()->id())->unique()->values();

        $group->fill($validated);
        $group->save();

        if ($userIds->isNotEmpty()) {
            $syncData = $userIds->mapWithKeys(fn($id) => [$id => ['joined_at' => now()]]);
            $group->users()->sync($syncData);
        }

        return response()->json('', 200);
    }

    public function leave(Request $request, Group $group): JsonResponse
    {
        $user = auth()->user();

        $group->users()->detach($user->id);

        return response()->json();
    }
}
