<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'status',
        'notes',
        'symptoms',
        'diagnosis',
        'treatment',
        'prescription',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
            ->whereIn('status', ['scheduled', 'confirmed']);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('d/m/y H:i');
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'scheduled' => 'Agendado',
            'confirmed' => 'Confirmado',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            'no_show' => 'Faltou'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) && $this->appointment_date > now();
    }
}
