<?php 

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;


class TsaludPersonalAccessToken extends PersonalAccessToken 
{

    protected $table = 'tsalud_personal_access_tokens';

}