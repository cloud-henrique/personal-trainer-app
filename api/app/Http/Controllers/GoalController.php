<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * GoalController
 *
 * Manages student goals.
 * All queries are automatically filtered by tenant_id via BelongsToTenant trait.
 */
class GoalController extends Controller
{
    /**
     * List all goals for a student.
     *
     * @param Student $student
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Student $student, Request $request): JsonResponse
    {
        $goals = Goal::where('student_id', $student->id)
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $goals,
        ]);
    }

    /**
     * Create a new goal for a student.
     *
     * @param Request $request
     * @param Student $student
     * @return JsonResponse
     */
    public function store(Request $request, Student $student): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:weight_loss,muscle_gain,performance,other',
                'target_value' => 'nullable|numeric',
                'current_value' => 'nullable|numeric',
                'unit' => 'nullable|string|max:50',
                'starts_at' => 'required|date',
                'target_date' => 'nullable|date|after:starts_at',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $goal = Goal::create([
                ...$request->all(),
                'student_id' => $student->id,
                'status' => 'active',
            ]);

            Log::channel('students')->info('Goal created', [
                'goal_id' => $goal->id,
                'student_id' => $student->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $goal,
                'message' => 'Meta criada com sucesso',
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::channel('students')->error('Goal creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar meta',
            ], 500);
        }
    }

    /**
     * Update goal.
     *
     * @param Request $request
     * @param Goal $goal
     * @return JsonResponse
     */
    public function update(Request $request, Goal $goal): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'sometimes|required|in:weight_loss,muscle_gain,performance,other',
                'target_value' => 'nullable|numeric',
                'current_value' => 'nullable|numeric',
                'unit' => 'nullable|string|max:50',
                'starts_at' => 'sometimes|required|date',
                'target_date' => 'nullable|date|after:starts_at',
                'completed_at' => 'nullable|date',
                'status' => 'sometimes|in:active,completed,cancelled',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // If marking as completed, set completed_at if not provided
            if ($request->status === 'completed' && !$request->completed_at) {
                $request->merge(['completed_at' => now()]);
            }

            $goal->update($request->all());

            Log::channel('students')->info('Goal updated', [
                'goal_id' => $goal->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $goal->fresh(),
                'message' => 'Meta atualizada com sucesso',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::channel('students')->error('Goal update failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar meta',
            ], 500);
        }
    }

    /**
     * Delete goal.
     *
     * @param Goal $goal
     * @return JsonResponse
     */
    public function destroy(Goal $goal): JsonResponse
    {
        try {
            $goal->delete();

            Log::channel('students')->info('Goal deleted', [
                'goal_id' => $goal->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meta excluída com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('students')->error('Goal deletion failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir meta',
            ], 500);
        }
    }
}
