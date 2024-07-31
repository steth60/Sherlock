<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'weight'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function childGroups()
    {
        return $this->belongsToMany(Group::class, 'group_group', 'parent_id', 'child_id');
    }
}
