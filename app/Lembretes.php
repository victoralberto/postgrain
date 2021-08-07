<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lembretes extends Model
{
    //
    protected $fillable = [
        'username', 'titulo', 'descricao', 'data_lembrete', 'repetir', 'status', 'ativo'
    ];
}
