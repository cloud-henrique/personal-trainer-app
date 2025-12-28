<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workout\StoreWorkoutRequest;
use App\Http\Requests\Workout\UpdateWorkoutRequest;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WorkoutController
 *
 * Manages workout plan CRUD operations.
 * All queries are automatically filtered by tenant_id via BelongsToTenant trait.
 */
class WorkoutController extends Controller
{
    /**
     * List all workouts (optionally filtered by student).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $workouts = Workout::query()
            ->with(['student:id,name', 'creator:id,name', 'exercises'])
            ->when($request->student_id, function ($query, $studentId) {
                $query->where('student_id', $studentId);
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $workouts,
        ]);
    }

    /**
     * Create a new workout.
     *
     * @param StoreWorkoutRequest $request
     * @return JsonResponse
     */
    public function store(StoreWorkoutRequest $request): JsonResponse
    {
        try {
            $workout = Workout::create([
                ...$request->validated(),
                'created_by' => auth()->id(),
            ]);

            Log::channel('workouts')->info('Workout created', [
                'workout_id' => $workout->id,
                'student_id' => $workout->student_id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $workout->load(['student:id,name', 'creator:id,name']),
                'message' => 'Treino criado com sucesso',
            ], 201);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Workout creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar treino',
            ], 500);
        }
    }

    /**
     * Show workout details with exercises.
     *
     * @param Workout $workout
     * @return JsonResponse
     */
    public function show(Workout $workout): JsonResponse
    {
        $workout->load([
            'student:id,name,email',
            'creator:id,name',
            'exercises',
        ]);

        return response()->json([
            'success' => true,
            'data' => $workout,
        ]);
    }

    /**
     * Update workout.
     *
     * @param UpdateWorkoutRequest $request
     * @param Workout $workout
     * @return JsonResponse
     */
    public function update(UpdateWorkoutRequest $request, Workout $workout): JsonResponse
    {
        try {
            $workout->update($request->validated());

            Log::channel('workouts')->info('Workout updated', [
                'workout_id' => $workout->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $workout->fresh()->load(['student:id,name', 'creator:id,name', 'exercises']),
                'message' => 'Treino atualizado com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Workout update failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar treino',
            ], 500);
        }
    }

    /**
     * Delete workout (soft delete).
     *
     * @param Workout $workout
     * @return JsonResponse
     */
    public function destroy(Workout $workout): JsonResponse
    {
        try {
            $workout->delete();

            Log::channel('workouts')->info('Workout deleted', [
                'workout_id' => $workout->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treino excluído com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Workout deletion failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir treino',
            ], 500);
        }
    }
}
