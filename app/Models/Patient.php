<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
        'birth_date',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'medical_history',
        'allergies',
        'medications',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }

    public function getFormattedCpfAttribute()
    {
        $cpf = $this->cpf;
        return substr($cpf, 0, 3) . '.' .
            substr($cpf, 3, 3) . '.' .
            substr($cpf, 6, 3) . '-' .
            substr($cpf, 9, 2);
    }
}
