<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSite extends Model
{
    use HasFactory;

    public function User(){
        return $this->hasOne(User::class, 'UserId');
    }
    
    public function Site(){
        return $this->hasOne(Site::class, 'SiteId');
    }
}
