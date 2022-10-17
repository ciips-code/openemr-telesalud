<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TsaludIdProfesional extends Model
{
    use HasFactory;

    protected $fillable = [
        'identificador_profesional',
        'user_id'
    ];
}
