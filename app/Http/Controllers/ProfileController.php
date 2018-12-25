<?php

namespace Knot\Http\Controllers;

use Image;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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

        $image = Image::make($file)->encode('jpg', 80);

        $image->fit(600, 600, function ($constraint) {
            $constraint->upsize();
        });

        $thumbName = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->getClientOriginalExtension();
        $publicPath = public_path('images/tmp/avatars/'.$thumbName);
        $image->save($publicPath);

        // Upload it to the cloud from the public folder
        $cloudFile = new File($publicPath);
        $cloudUrl = Storage::cloud()->putFile('avatars', $cloudFile, 'public');

        auth()->user()->update(['profile_image' => $cloudUrl]);

        unlink($publicPath);
        $image->destroy();

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
