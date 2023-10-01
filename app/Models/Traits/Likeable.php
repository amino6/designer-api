<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable {
    public function likes() {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like() {
        if(! auth()->check()) {
            return;
        }

        if($this->alreadyLikedByUser()) {
            return;
        }

        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function unlike() {
        if(! auth()->check()) {
            return;
        }

        if(!$this->alreadyLikedByUser()) {
            return;
        }

        $this->likes()->where('user_id',auth()->id())->delete();
    }

    private function alreadyLikedByUser() {
        return (bool) $this->likes()->where('user_id',auth()->id())->count();
    }
}
