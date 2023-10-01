<?php

namespace App\Http\Controllers\Api\Designs;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDesignRequest;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DesignController extends Controller
{
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

        return new DesignResource($design);
    }
}
