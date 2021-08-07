<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    //

    protected $table = "notificacao";

    protected $fillable = [
        'id_lembrete', 'status'
    ];
}
