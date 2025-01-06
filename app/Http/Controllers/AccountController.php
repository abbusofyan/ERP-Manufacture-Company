<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show(): \Inertia\Response
    {
        $user = Auth::user();
        return Inertia::render('Account/Index', [
            'user' => $user,
        ]);
    }


    /**
     * profileEdit page
     *
     * @return \Inertia\Response
     */
    public function edit(): \Inertia\Response
    {
        $user = Auth::user();
        return Inertia::render('Account/Form', [
            'user' => $user,
        ]);
    }

    /**
     * overview update
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request): Application|RedirectResponse|Redirector
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id . ',id',
            'email' => 'required|unique:users,email,' . $user->id . ',id',
            'phone' => 'required|phone_number',
            'image' => 'image|mimes:jpeg,png,jpg|max:10240'
        ], [
            'name.required' => 'Please enter a name.',
            'username.required' => 'Please enter a username.',
            'username.unique' => 'This username is already in use.',
            'phone.required' => 'Please enter a phone number.',
            'phone.phone_number' => 'Phone number is invalid format.',
            'email.required' => 'Please enter a email address.',
            'email.numeric' => 'Email address is invalid format.',
            'email.unique' => 'This email address is already in use.',
        ]);

        $data = $request->except('role', 'password');
        $user->update($data);


        /** Update avatar - start **/
        if (!empty($user->avatar) && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store("users/$user->id", 'public');
            $user->update(['avatar' => $path]);
        }
        /** Update avatar - end **/

        Auth::user()->fresh();

        return redirect('/account')->with('updated', 'Account updated successfully');
    }


    /**
     * profileEdit page
     *
     * @return \Inertia\Response
     */
    public function editPassword(): \Inertia\Response
    {
        $user = Auth::user();
        return Inertia::render('Account/Password/Form', [
            'user' => $user,
        ]);
    }

    /**
     * overview update
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function updatePassword(Request $request): Application|RedirectResponse|Redirector
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'different:current_password',
                'regex:/^(?=.*[a-z])(?=.*[\d\W]).{8,}$/',
            ],
            'confirm_new_password' => 'required|same:new_password',
        ], [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'New password must be at least 8 characters long.',
            'new_password.different' => 'New password must be different from the current password.',
            'new_password.regex' => 'New password must contain at least one lowercase character and one number, symbol, or whitespace character.',
            'confirm_new_password.required' => 'Please confirm your new password.',
            'confirm_new_password.same' => 'Passwords do not match.',
        ]);


        if (Hash::check($request->current_password, $user->password)) {
            $user->password = $request->new_password;
            $user->save();

            return redirect('/account')->with('updated', 'Your password has been changed successfully.');
        } else {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
    }
}
