<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'tagline' => ['string'],
            'name' => ['required'],
            'about' => ['string', 'min:20'],
            'formatted_address' => ['string'],
            'location.latitude' => ['numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['numeric', 'min:-180', 'max:180']
        ]);

        if ($request->location)
            $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user = auth()->user()->update([
            'name' => $request->name,
            'formatted_address' => $request->formatted_address ?? auth()->user()->formatted_address,
            'location' => $location ?? auth()->user()->location,
            'available_to_hire' => $request->available_to_hire ?? auth()->user()->available_to_hire,
            'about' => $request->about ?? auth()->user()->about,
            'tagline' => $request->tagline ?? auth()->user()->tagline,
        ]);

        return response()->json([
            "data" => $user
        ]);
    }

    public function search(Request $request)
    {
        $designers = User::query()
            ->search($request)
            ->get();

        return UserResource::collection($designers);
    }
}
