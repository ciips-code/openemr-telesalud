<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'facility';

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


}