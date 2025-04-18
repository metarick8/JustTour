<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $table = "site";
    protected $fillable = [
        "SiteName",
        "Location",
        "Details",
    ];

    public function UserSite(){
        return $this->hasMany(UserSite::class, 'SiteId');
    }

    public function SitePhotos(){
        return $this->hasMany(SitePhotos::class, 'SitePhotosId');
    }
}
