<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relationship (if a contract belongs to a type)
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
