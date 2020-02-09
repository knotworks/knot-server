<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Image;
use JD\Cloudder\Facades\Cloudder;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    /**
     * Set the user's avatar.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $this->validate($request, ['avatar' => 'required|image|max:5000']);

        $file = $request->file('avatar');

        // Move it to the public folder
        $name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path = 'images/tmp/avatars/'.$name.'.jpg';

        $image = Image::make($file)->encode('jpg', 80);

        $image->fit(600, 600, function ($constraint) {
            $constraint->upsize();
        });

        $image->save(public_path($path));

        Cloudder::upload(public_path($path), 'avatars/'.$name);

        // Destroy the image instance
        $image->destroy();

        auth()->user()->update(['profile_image' => Cloudder::getPublicId()]);

        unlink(public_path($path));

        return auth()->user();
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'current_password' => 'required_with:password',
            'password' => 'confirmed',
        ]);

        $user->fill($request->only('first_name', 'last_name', 'email'))->save();

        if ($request->has('current_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->fill(['password' => $request->password])->save();
            } else {
                return response()->json(['error' => 'The provided current password did not match our records.'], 422);
            }
        }

        return $user;
    }
}
