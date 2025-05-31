<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient:id,name,phone,email', 'doctor:id,name,specialty']);

        if ($request->has('status') && !empty($request->status)) {
            $query->byStatus($request->status);
        }

        if ($request->has('patient_id') && !empty($request->patient_id)) {
            $query->byPatient($request->patient_id);
        }

        $sortBy = $request->get('sort_by', 'appointment_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $appointments = $query->paginate($perPage);

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([$validatedData]);
        $appointment->load(['patient:id,name,phone,email', 'doctor:id,name,specialty']);

        return response()->json([
            'message' => 'Consulta agendada com sucesso',
            'appointment' => $appointment
        ], 201);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'doctor:id,name,specialty,crm'])
            ->findOrFail($id);

        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'doctor_id' => 'sometimes|exists:users,id',
            'appointment_date' => 'sometimes|date',
            'status' => 'sometimes|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
        ]);

        $appointment->update($request->all());
        $appointment->load(['patient:id,name,phone,email', 'doctor:id,name,specialty']);

        return response()->json([
            'message' => 'Consulta atualizada com sucesso',
            'appointment' => $appointment
        ]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if (!$appointment->canBeCancelled()) {
            return response()->json([
                'message' => 'Esta consulta não pode ser cancelada'
            ], 422);
        }

        $appointment->delete();

        return response()->json([
            'message' => 'Consulta excluída com sucesso'
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if (!$appointment->canBeCancelled()) {
            return response()->json([
                'message' => 'Esta consulta não pode ser cancelada'
            ], 422);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Consulta cancelada com sucesso',
            'appointment' => $appointment
        ]);
    }

    public function confirm($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Apenas consultas agendadas podem ser confirmadas'
            ], 422);
        }

        $appointment->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Consulta confirmada com sucesso',
            'appointment' => $appointment
        ]);
    }

    public function start($id)
    {
        $appointment = Appointment::findOrFail($id);

        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return response()->json([
                'message' => 'Esta consulta não pode ser iniciada'
            ], 422);
        }

        $appointment->update(['status' => 'in_progress']);

        return response()->json([
            'message' => 'Consulta iniciada',
            'appointment' => $appointment
        ]);
    }

    public function complete(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status !== 'in_progress') {
            return response()->json([
                'message' => 'Apenas consultas em andamento podem ser finalizadas'
            ], 422);
        }

        $request->validate([
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $appointment->update([
            'status' => 'completed',
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Consulta finalizada com sucesso',
            'appointment' => $appointment
        ]);
    }
}
