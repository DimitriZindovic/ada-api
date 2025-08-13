<?php

namespace App\Models;

use App\Http\Resources\User as ResourcesUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const RESOURCE = ResourcesUser::class;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
                    ->withPivot('joined_at');
    }
}
