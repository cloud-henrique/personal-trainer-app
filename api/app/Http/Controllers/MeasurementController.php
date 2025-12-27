<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentMeasurement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * MeasurementController
 *
 * Manages student physical measurements.
 * All queries are automatically filtered by tenant_id via BelongsToTenant trait.
 */
class MeasurementController extends Controller
{
    /**
     * List all measurements for a student.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function index(Student $student): JsonResponse
    {
        $measurements = StudentMeasurement::where('student_id', $student->id)
            ->orderBy('measured_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $measurements,
        ]);
    }

    /**
     * Create a new measurement for a student.
     *
     * @param Request $request
     * @param Student $student
     * @return JsonResponse
     */
    public function store(Request $request, Student $student): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'weight' => 'required|numeric|min:1|max:999.99',
                'body_fat' => 'nullable|numeric|min:0|max:100',
                'muscle_mass' => 'nullable|numeric|min:0|max:999.99',
                'chest' => 'nullable|numeric|min:0|max:999.99',
                'waist' => 'nullable|numeric|min:0|max:999.99',
                'hips' => 'nullable|numeric|min:0|max:999.99',
                'right_arm' => 'nullable|numeric|min:0|max:999.99',
                'left_arm' => 'nullable|numeric|min:0|max:999.99',
                'right_thigh' => 'nullable|numeric|min:0|max:999.99',
                'left_thigh' => 'nullable|numeric|min:0|max:999.99',
                'right_calf' => 'nullable|numeric|min:0|max:999.99',
                'left_calf' => 'nullable|numeric|min:0|max:999.99',
                'notes' => 'nullable|string',
                'measured_at' => 'required|date',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $measurement = StudentMeasurement::create([
                ...$request->all(),
                'student_id' => $student->id,
            ]);

            Log::channel('students')->info('Measurement created', [
                'measurement_id' => $measurement->id,
                'student_id' => $student->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $measurement,
                'message' => 'Avaliação física registrada com sucesso',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::channel('students')->error('Measurement creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar avaliação física',
            ], 500);
        }
    }

    /**
     * Get latest measurement for a student.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function latest(Student $student): JsonResponse
    {
        $measurement = StudentMeasurement::where('student_id', $student->id)
            ->orderBy('measured_at', 'desc')
            ->first();

        if (!$measurement) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma avaliação física encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $measurement,
        ]);
    }

    /**
     * Get measurement data for graphs (weight evolution).
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function graph(Student $student): JsonResponse
    {
        $measurements = StudentMeasurement::where('student_id', $student->id)
            ->orderBy('measured_at', 'asc')
            ->get(['weight', 'body_fat', 'muscle_mass', 'measured_at']);

        // Format data for charts
        $graphData = [
            'labels' => $measurements->pluck('measured_at')->map(fn($date) => $date->format('d/m/Y'))->toArray(),
            'weight' => $measurements->pluck('weight')->toArray(),
            'body_fat' => $measurements->pluck('body_fat')->toArray(),
            'muscle_mass' => $measurements->pluck('muscle_mass')->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $graphData,
        ]);
    }
}
