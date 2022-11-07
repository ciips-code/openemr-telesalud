<?php

namespace App\Models;

use App\Models\{Patient, TsaludVc};
use Illuminate\Database\Eloquent\Model;

class CalendarEvents extends Model
{
    
    protected $table = 'openemr_postcalendar_events';


    public function patient()
    {
        return $this->hasOne(Patient::class, 'id', 'pc_pid');
    }

    public function videoCall()
    {
        return $this->hasOne(TsaludVc::class, 'pc_eid', 'pc_eid');
    }
    
}