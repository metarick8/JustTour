<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitePhotos extends Model
{
    use HasFactory;

    public function Site(){
        return $this->hasOne(Site::class, 'SiteId');
    }
}
