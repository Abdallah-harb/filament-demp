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


    /**
     * Check if the role is assigned to any users
     */
    public function hasUsers(): bool
    {
        return $this->users()->exists();
    }

    /**
     * Get the count of users assigned to this role
     */
    public function getUsersCount(): int
    {
        return $this->users()->count();
    }
}
