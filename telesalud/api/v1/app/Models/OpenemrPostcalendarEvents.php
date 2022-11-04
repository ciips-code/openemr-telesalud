<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;

class OpenemrPostCalendarEvents extends Model
{
    
    protected $table = 'openemr_postcalendar_events';


    public function Patient()
    {
        return $this->hasOne(Patient::class, 'id', 'pc_pid');
    }
    
}