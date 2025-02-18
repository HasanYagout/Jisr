<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id','id');
    }
}
