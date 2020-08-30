<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;
use JD\Cloudder\Facades\Cloudder;
use Knot\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(User $user)
    {
        $this->authorize('can_view_profile', $user);

        return [
            'user' => $user,
            'posts' => $user->feed(),
        ];
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
        $nameWithExtension = $name.'.jpg';

        $image = Image::make($file)->fit(300, 300, function ($constraint) {
            $constraint->upsize();
        })->encode('jpg', 90);

        Storage::disk('avatars')->put($nameWithExtension, $image);

        try {
            $filePath = Storage::disk('avatars')->path($nameWithExtension);
            $uploadPath = config('app.env').'/avatars/'.$name;
            $publicId = Cloudder::upload($filePath, $uploadPath, ['angle' => 0])->getPublicId();

            auth()->user()->update(['profile_image' => $publicId]);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $image->destroy();
            Storage::disk('avatars')->delete($nameWithExtension);
        }

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
