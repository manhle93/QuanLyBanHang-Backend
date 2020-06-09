<?php

namespace App\Http\Controllers\System;

use App\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page',10);
        $page = $request->get('page',1);
        $query=Role::query();   
        $search = $request->get('search'); 
        if(isset($search)){
            $search = trim($search);
            $query->where('name','ilike', "%{$search}%");
            $query->orWhere('code','ilike', "%{$search}%");
        }
        $orders =$query->paginate($perPage, ['*'], 'page', $page);
         return response()->json([
            'data' => $orders,
            'message' => 'Lấy dữ liệu thành công',
            'code' => 200,
        ], 200);
    }

    public function store(RoleRequest $request)
    {
        // return $request;
        $role = Role::create($request->all());
        return response($role, Response::HTTP_CREATED);
    }

    public function update(Role $role, RoleRequest $request)
    {
        $role->update($request->all());
        return response('updated', Response::HTTP_ACCEPTED);
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response(['message' => 'Bạn không thể xóa role này'], Response::HTTP_BAD_REQUEST);
        }
    }
}
