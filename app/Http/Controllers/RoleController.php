<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function getMenus()
    {
        return ['data' => Menu::menu()->with('children')->get()];
    }
    public function addMenuToRole(Role $role, Request $request)
    {
        $role->menus()->detach();
        $role->menus()->attach($request->all());
    }
    public function getMenuRole()
    {
        return ['data' => Menu::with('roles')->get()];
    }
    public function getRoleMenu()
    {
        return ['data' => Role::with('menus')->get()];
    }
}
