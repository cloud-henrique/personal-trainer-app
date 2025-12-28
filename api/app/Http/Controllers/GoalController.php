<?php

namespace App\Http\Controllers;

use App\Http\Requests\Goal\StoreGoalRequest;
use App\Http\Requests\Goal\UpdateGoalRequest;
use App\Models\Goal;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
     * @param StoreGoalRequest $request
     * @param Student $student
     * @return JsonResponse
     */
    public function store(StoreGoalRequest $request, Student $student): JsonResponse
    {
        try {
            $goal = Goal::create([
                ...$request->validated(),
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
     * @param UpdateGoalRequest $request
     * @param Goal $goal
     * @return JsonResponse
     */
    public function update(UpdateGoalRequest $request, Goal $goal): JsonResponse
    {
        try {
            // UpdateGoalRequest already handles the completed_at logic in withValidator
            $goal->update($request->validated());

            Log::channel('students')->info('Goal updated', [
                'goal_id' => $goal->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $goal->fresh(),
                'message' => 'Meta atualizada com sucesso',
            ]);

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
