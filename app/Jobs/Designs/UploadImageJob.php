<?php

namespace App\Jobs\Designs;

use Image;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Design $design)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $original_img = storage_path() . '/uploads/original/' . $this->design->image;

        try {
            // create the large image
            $this->resize($original_img, 800, 600)
                ->save($large = storage_path('uploads/large/' . $this->design->image));

            // create the thumbnail
            $this->resize($original_img, 250, 200)
                ->save($thumbnail = storage_path('uploads/thumbnail/' . $this->design->image));

            // move images from tmp to permanent disk
            if ($this->move_img_to($this->design->disk, 'uploads/designs/original/' . $this->design->image, $original_img)) {
                Storage::disk('tmp')->delete("/uploads/original/" . $this->design->image);
            }

            if ($this->move_img_to($this->design->disk, 'uploads/designs/large/' . $this->design->image, $large)) {
                Storage::disk('tmp')->delete("/uploads/large/" . $this->design->image);
            }

            if ($this->move_img_to($this->design->disk, 'uploads/designs/thumbnail/' . $this->design->image, $thumbnail)) {
                Storage::disk('tmp')->delete("/uploads/thumbnail/" . $this->design->image);
            }

            // change design status
            $this->design->update([
                'upload_successful' => true
            ]);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    private function resize($image, $width, $height = null)
    {
        return Image::make($image)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    private function move_img_to($disk, $path, $img)
    {
        return Storage::disk($disk)->put($path, fopen($img, "r+"));
    }
}
