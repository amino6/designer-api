<?php

namespace App\Http\Controllers\Api\Designs;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDesignRequest;
use App\Models\Design;
use App\Jobs\Designs\UploadImageJob;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(UploadDesignRequest $request)
    {
        $image = $request->validated()["image"];
        $img_path = $image->getPathname();

        $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        // move image to tmp folder
        $tmp = $image->storeAs('uploads/designs/original', $filename, config('site.upload_disk'));

        $design = Design::create([
            'image' => $filename,
            'disk' => config('site.upload_disk'),
            "user_id" => auth()->id()
        ]);

        // dispatch image manipulation job
        //$this->dispatch(new UploadImageJob($design));
        UploadImageJob::dispatch($design);

        return response()->json($design, 200);
    }
}
