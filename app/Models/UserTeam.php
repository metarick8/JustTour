<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    use HasFactory;
    protected $table = 'user_team';
    protected $fillable = [
        'TeamId',
        'UserId'
    ];
    public function User()
    {
        return $this->belongsTo(User::class, 'UserId');
    }

    public function Team()
    {
        return $this->belongsTo(Team::class, 'TeamId'); 
    }
}
