<?php

namespace App\Http\Controllers;

use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Resources\StudentMeasurementResource;
use App\Models\Student;
use App\Models\StudentMeasurement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
            'data' => StudentMeasurementResource::collection($measurements),
        ]);
    }

    /**
     * Create a new measurement for a student.
     *
     * @param StoreMeasurementRequest $request
     * @param Student $student
     * @return JsonResponse
     */
    public function store(StoreMeasurementRequest $request, Student $student): JsonResponse
    {
        try {
            $measurement = StudentMeasurement::create([
                ...$request->validated(),
                'student_id' => $student->id,
            ]);

            Log::channel('students')->info('Measurement created', [
                'measurement_id' => $measurement->id,
                'student_id' => $student->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => new StudentMeasurementResource($measurement),
                'message' => 'Avaliação física registrada com sucesso',
            ], 201);

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
            'data' => new StudentMeasurementResource($measurement),
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
