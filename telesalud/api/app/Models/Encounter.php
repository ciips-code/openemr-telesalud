<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_encounter';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    public function patient()
    {
        return $this->belongsTo(Patient::class, 'pid', 'pid');
    }

}