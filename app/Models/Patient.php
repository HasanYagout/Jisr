<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{


    public function examination()
    {
        return $this->hasOne(Examination::class,'patient_id','id');
    }
}
