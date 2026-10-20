<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function debts()
    {
        return $this->hasMany(Debt::class, 'category_id');
    }
}