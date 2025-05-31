<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return response()->json($patients);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients',
            'phone' => 'required|string',
            'cpf' => 'required|string|size:11|unique:patients',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:M,F,O',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string',
        ]);

        $patient = Patient::create([$validatedData]);

        return response()->json([
            'message' => 'Paciente criado com sucesso',
            'patient' => $patient
        ], 201);
    }

    public function show($id)
    {
        $patient = Patient::with(['appointments' => function($query) {
            $query->with('doctor:id,name,specialty')
                  ->orderBy('appointment_date', 'desc');
        }])->findOrFail($id);

        return response()->json($patient);
    }

    public function update(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => ['sometimes', 'email', Rule::unique('patients')->ignore($patient->id)],
            'phone' => 'sometimes|string',
            'cpf' => ['sometimes', 'string', 'size:11', Rule::unique('patients')->ignore($patient->id)],
            'birth_date' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:M,F,O',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|size:2',
            'zip_code' => 'sometimes|string|size:8',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact_name' => 'sometimes|string|max:255',
            'emergency_contact_phone' => 'sometimes|string',
            'active' => 'sometimes|boolean',
        ]);

        $patient->update($validatedData);

        return response()->json([
            'message' => 'Paciente atualizado com sucesso',
            'patient' => $patient
        ]);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        $patient->delete();

        return response()->json([
            'message' => 'Paciente excluído com sucesso'
        ]);
    }
}
