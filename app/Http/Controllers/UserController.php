<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function select2Query(Request $request)
    {
        return User::when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%")
            ->orWhere('username', 'LIKE', "%{$request->search}%");
        })->limit(10)->get(['id', 'name', 'username']);
    }

    public function select2QueryManager(Request $request)
    {
        return User::permission('approve-requisition')->when($request->has('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        })->limit(10)->get(['id', 'name']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
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

        $users = User::with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $users->appends($queryString);

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        $roles = self::toSelect2Data(Role::all()->toArray(), 'id', 'name');

        return Inertia::render('User/Form', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'phone' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ], [
            'name.required' => 'Please enter a name.',
            'username.required' => 'Please enter a username.',
            'username.unique' => 'This username is already in use.',
            'phone.required' => 'Please enter a phone number.',
            'phone.phone_number' => 'Phone number is invalid format.',
            'email.required' => 'Please enter a email address.',
            'email.numeric' => 'Email address is invalid format.',
            'email.unique' => 'This email address is already in use.',
            'role.required' => 'Please select a role for the user.',
        ]);

        $user = User::create($request->except('role'));

        $user->assignRole($request->role);

        if ($request->has('email_verified_at')) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect('/users')->with('created', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Inertia\Response
     */
    public function show(User $user)
    {
        $userWithRole = User::with('roles')->find($user->id);
        return Inertia::render('User/View', [
            'user' => $userWithRole,
            'qr_code' => $user->generateQrCode(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        $userRole = $user->getRoleNames()->first();
        $roles = Role::get();

        return Inertia::render('User/Form', [
            'user' => $user,
            'userRole' => $userRole,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'role' => 'required',
            'username' => 'required|unique:users,username,' . $user->id . ',id',
            'email' => 'required|unique:users,email,' . $user->id . ',id',
            'phone' => 'required',
            'password' => 'sometimes|confirmed'
        ]);

        $data = $request->except('role', 'password');

        if ($request->password) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        $user->roles()->detach();

        $user->assignRole($request->role);

        if ($request->has('email_verified_at') && empty($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect('/users')->with('updated', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->roles()->detach();

        $user->delete();

        return back()->with('deleted', 'User deleted successfully');
    }
}
