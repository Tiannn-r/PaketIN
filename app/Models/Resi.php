<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resi extends Model
{
    protected $table = 'resi';
    protected $primaryKey = 'tanggal';
    public $increment = false;
    protected $keyType = 'string';
    public $timestamps = false;

}
