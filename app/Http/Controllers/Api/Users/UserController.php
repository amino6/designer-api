<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserContactsRequest;
use App\Http\Resources\DesignResource;
use App\Http\Resources\FullUserResource;
use App\Http\Resources\UserResource;
use App\Models\Design;
use App\Models\User;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UserController extends Controller
{
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        if ($request->location)
            $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user = auth()->user()->update([
            'formatted_address' => $request->formatted_address ?? auth()->user()->formatted_address,
            'location' => $location ?? auth()->user()->location,
            'available_to_hire' => isset($request->available_to_hire) ? $request->available_to_hire : auth()->user()->available_to_hire,
            'about' => $request->about ?? auth()->user()->about,
            'tagline' => $request->tagline ?? auth()->user()->tagline,
            'contact_email' => $request->contact_email ?? auth()->user()->contact_email,
            'website_url' => $request->website_url ?? auth()->user()->website_url,
        ]);

        return response()->json([
            "data" => $user
        ]);
    }

    public function updateContacts(UpdateUserContactsRequest $request)
    {
        $user = auth()->user()->update([
            'contact_email' => $request->contact_email,
            'website_url' => $request->website_url,
        ]);

        return response()->json([
            "data" => $user
        ]);
    }

    public function search(Request $request)
    {
        $designers = User::query()
            ->search($request)
            ->withCount(['designs' => function ($query) {
                $query->where('is_live', true);
            }])
            ->paginate(12);

        return UserResource::collection($designers);
    }

    public function getUserInfo(String|Int $id)
    {
        $user = User::withCount(['designs' => function ($q) {
                return $q->where('is_live', true);
            }])->findOrFail($id);

        $likes_count = User::find($id)->designs()->withCount('likes')->where('is_live', true)->pluck('likes_count')->reduce(function (?int $carry, int $item) {
            return $carry + $item;
        }) ?? 0;

        return response()->json([
            new FullUserResource($user),
            $likes_count
        ]);
    }

    public function getUserDesigns(User $user)
    {
        $designs = $user->designs()->where('is_live', true)->with(["user", "likes"])->paginate(12);
        return DesignResource::collection($designs);
    }

    public function getDesigns(Request $request)
    {
        $designs = auth()->user()->designs()
            ->with(["likes", "tags"]);

        if ($request->q) {
            $designs = $designs->where(function ($q) use ($request) {
                $q->whereHas("tags", function ($q) use ($request) {
                    $q->where('name', '=', $request->q);
                })->orWhere('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if (isset($request->order) && isset($request->dir)) {
            if (
                in_array($request->dir, ['asc', 'desc']) &&
                in_array($request->order, ['title'])
            ) {
                $designs = $designs->orderBy($request->order, $request->dir);
            }

            if (
                in_array($request->dir, ['asc', 'desc']) &&
                in_array($request->order, ['likes'])
            ) {
                $designs = $designs->withCount("likes")->orderBy("likes_count", $request->dir);
            }
        }

        $designs = $designs->paginate(10);

        return DesignResource::collection($designs);
    }

    public function getLikedDesigns()
    {
        $designs = Design::with([
            "likes",
            "tags",
            "team",
            "user"
        ])->whereHas('likes', function ($query) {
            $query->where('user_id', '=', auth()->id());
        })->paginate(12);

        return DesignResource::collection($designs);
    }
}
