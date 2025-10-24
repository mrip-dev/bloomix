<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $fillable = [
        'id',
        'name',
    ];


    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
