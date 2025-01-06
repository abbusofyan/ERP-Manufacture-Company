<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('view-any');

        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->paginate) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $roles = Role::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $roles->appends($queryString);

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function create(): \Inertia\Response
    {
        $this->authorize('view-any');

        $permissions = Permission::get();

        return Inertia::render('Role/Form', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('view-any');

        $request->validate([
            'name' => 'required|unique:roles',
        ]);

        $role = Role::create($request->all());

        $permissionId = Permission::whereIn('name', $request->permission_data)->pluck('id');

        $role->givePermissionTo($permissionId);

        return redirect('/roles')->with('created', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Role $role
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return \Inertia\Response
     * @throws AuthorizationException
     */
    public function edit(Role $role)
    {
        $this->authorize('view-any');

        $rolePermissions = $role->permissions->pluck('name');

        return Inertia::render('Role/Form', [
            'role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Role $role
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Role $role): Redirector|RedirectResponse|Application
    {
        $this->authorize('view-any');

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . ',id',
        ]);

        $role->update($request->all());

        $permissionId = Permission::whereIn('name', $request->permission_data)->pluck('id');

        $role->permissions()->sync($permissionId);

        return redirect('/roles')->with('updated', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role)
    {
        $this->authorize('view-any');

        $role->delete();

        return back()->with('deleted', 'Role deleted successfully');
    }
}
