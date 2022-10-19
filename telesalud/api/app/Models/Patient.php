<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patient_data';

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

    protected $connection = 'mysql2';

    /**
     * Relación de Pacientes -> Consultas
     */
    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'pid', 'pid');
    }

}