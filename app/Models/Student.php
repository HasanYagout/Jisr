<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'user_id', 'user_id');
    }

}
