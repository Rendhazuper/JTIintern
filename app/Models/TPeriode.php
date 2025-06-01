<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPeriode extends Model
{
    protected $table = 't_periode';
    protected $fillable = ['periode_id'];

    public static function setActivePeriod($periodeId)
    {
        // Clear existing record
        self::query()->delete();
        
        // Insert new record
        return self::create(['periode_id' => $periodeId]);
    }

    public static function getActivePeriod()
    {
        return self::first();
    }
}