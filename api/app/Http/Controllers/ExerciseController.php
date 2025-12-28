<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * ExerciseController
 *
 * Manages exercise CRUD operations within workouts.
 * All queries are automatically filtered by tenant_id via BelongsToTenant trait.
 */
class ExerciseController extends Controller
{
    /**
     * Create a new exercise for a workout.
     *
     * @param StoreExerciseRequest $request
     * @param Workout $workout
     * @return JsonResponse
     */
    public function store(StoreExerciseRequest $request, Workout $workout): JsonResponse
    {
        try {
            $data = $request->validated();

            // Get the next order number if not provided
            if (!isset($data['order'])) {
                $maxOrder = Exercise::where('workout_id', $workout->id)->max('order');
                $data['order'] = ($maxOrder ?? -1) + 1;
            }

            $exercise = Exercise::create([
                ...$data,
                'workout_id' => $workout->id,
            ]);

            Log::channel('workouts')->info('Exercise created', [
                'exercise_id' => $exercise->id,
                'workout_id' => $workout->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => new ExerciseResource($exercise),
                'message' => 'Exercício criado com sucesso',
            ], 201);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Exercise creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar exercício',
            ], 500);
        }
    }

    /**
     * Update exercise.
     *
     * @param UpdateExerciseRequest $request
     * @param Exercise $exercise
     * @return JsonResponse
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        try {
            $exercise->update($request->validated());

            Log::channel('workouts')->info('Exercise updated', [
                'exercise_id' => $exercise->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => new ExerciseResource($exercise->fresh()),
                'message' => 'Exercício atualizado com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Exercise update failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar exercício',
            ], 500);
        }
    }

    /**
     * Delete exercise.
     *
     * @param Exercise $exercise
     * @return JsonResponse
     */
    public function destroy(Exercise $exercise): JsonResponse
    {
        try {
            $exercise->delete();

            Log::channel('workouts')->info('Exercise deleted', [
                'exercise_id' => $exercise->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exercício excluído com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('workouts')->error('Exercise deletion failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir exercício',
            ], 500);
        }
    }
}
