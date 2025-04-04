<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'created' => fn() => $request->session()->get('created'),
                'updated' => fn() => $request->session()->get('updated'),
                'deleted' => fn() => $request->session()->get('deleted')
            ],
            'auth' => Auth::user() ? [
                'user' => [
                    'username' => Auth::user()->name,
                    'avatar' => Auth::user()->avatar,
                    'background' => Auth::user()->background,
                    'darkmode' => Auth::user()->dark_mode_text,
                    'status' => Auth::user()->status_text,
                    'role' => Auth::user()->getRoleNames()->first(),
                    'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
                ]
            ] : null,
            'gst_rate' => config('app.gst_rate'),
        ]);
    }
}
