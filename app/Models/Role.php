<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
    ];


    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->hasMany(Permission::class, 'role_id', 'id');
    }
}
