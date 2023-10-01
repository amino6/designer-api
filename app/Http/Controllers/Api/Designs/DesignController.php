<?php

namespace App\Http\Controllers\Api\Designs;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDesignRequest;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::with(["tags", "user"])->get();
        return DesignResource::collection($designs);
    }

    public function update(UpdateDesignRequest $request, Design $design)
    {
        $this->authorize("update", $design);

        $is_live = $design->upload_successful ? (filter_var($request->is_live, FILTER_VALIDATE_BOOLEAN)) : false;

        $design->update([
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "description" => $request->description,
            "is_live" => $is_live
        ]);

        $design->retag($request->tags);

        return new DesignResource($design);
    }

    public function destroy(Design $design)
    {
        $this->authorize('delete', $design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("/uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("/uploads/designs/{$size}/" . $design->image);
            }
        }

        $design->delete();

        return response()->json(status: 200);
    }
}
