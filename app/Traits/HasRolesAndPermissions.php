<?php

namespace App\Traits;
use App\Models\Role;
use App\Models\Permission;
trait HasRolesAndPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class,'users', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'roles_permissions', 'permission_id');
    }

    public function hasPermission($permission)
    {
        return (bool) $this->permissions->whereIn('slug', $permission)->count();
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission->slug);
    }

    public function getAllPermissionsByUser(array $permissions, $id)
    {
        return Permission::leftjoin('roles_permissions as r', 'r.permission_id', '=', 'permissions.id')->leftjoin('users as u', 'u.role_id', '=', 'r.role_id')->where('u.id', $id)->whereIn('permissions.slug',$permissions)->count();
    }

    public function getAllPermissionsByUser2($permissions, $id)
    {
        $permissions = explode('|', $permissions);
        return Permission::leftjoin('roles_permissions as r', 'r.permission_id', '=', 'permissions.id')->leftjoin('users as u', 'u.role_id', '=', 'r.role_id')->where('u.id', $id)->whereIn('permissions.slug',$permissions)->count();
    }

    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role){
            if($this->permissions()->where('permissions.slug', $role)->count()) {
                return true;
            }
        }

        return false;
    }

    public function hasRole(... $roles) {
        foreach ($roles as $role) {
            if ($this->roles()->where('roles.slug', $role)->count()) {
                return true;
            }
        }

        return false;
    }
}