<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserOrganization extends Model
{
    use HasFactory;

    protected $table = 'user_organization';
    protected $fillable = ["user_id", "organization_id", "points_get", "role_id"];

    /**
     * Get the role associated with the UserOrganization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
