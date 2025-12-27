<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
     * @param Request $request
     * @param Workout $workout
     * @return JsonResponse
     */
    public function store(Request $request, Workout $workout): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'muscle_group' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|url',
                'sets' => 'required|integer|min:1|max:20',
                'reps' => 'required|string|max:50',
                'rest' => 'required|string|max:50',
                'load' => 'nullable|string|max:50',
                'tempo' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'order' => 'sometimes|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Get the next order number if not provided
            if (!$request->has('order')) {
                $maxOrder = Exercise::where('workout_id', $workout->id)->max('order');
                $request->merge(['order' => ($maxOrder ?? -1) + 1]);
            }

            $exercise = Exercise::create([
                ...$request->all(),
                'workout_id' => $workout->id,
            ]);

            Log::channel('workouts')->info('Exercise created', [
                'exercise_id' => $exercise->id,
                'workout_id' => $workout->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $exercise,
                'message' => 'Exercício criado com sucesso',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

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
     * @param Request $request
     * @param Exercise $exercise
     * @return JsonResponse
     */
    public function update(Request $request, Exercise $exercise): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'muscle_group' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'nullable|url',
                'sets' => 'sometimes|required|integer|min:1|max:20',
                'reps' => 'sometimes|required|string|max:50',
                'rest' => 'sometimes|required|string|max:50',
                'load' => 'nullable|string|max:50',
                'tempo' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'order' => 'sometimes|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $exercise->update($request->all());

            Log::channel('workouts')->info('Exercise updated', [
                'exercise_id' => $exercise->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $exercise->fresh(),
                'message' => 'Exercício atualizado com sucesso',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

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
