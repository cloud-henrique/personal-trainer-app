<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Student;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * Provides dashboard statistics and recent activity for the authenticated tenant.
 */
class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'students' => [
                'total' => Student::count(),
                'active' => Student::where('is_active', true)->count(),
                'inactive' => Student::where('is_active', false)->count(),
            ],
            'workouts' => [
                'total' => Workout::count(),
                'active' => Workout::where('is_active', true)->count(),
                'by_category' => [
                    'strength' => Workout::where('category', 'strength')->count(),
                    'cardio' => Workout::where('category', 'cardio')->count(),
                    'flexibility' => Workout::where('category', 'flexibility')->count(),
                    'mixed' => Workout::where('category', 'mixed')->count(),
                ],
            ],
            'goals' => [
                'total' => Goal::count(),
                'active' => Goal::where('status', 'active')->count(),
                'completed' => Goal::where('status', 'completed')->count(),
            ],
            'workout_logs' => [
                'total' => WorkoutLog::count(),
                'this_month' => WorkoutLog::whereMonth('performed_at', now()->month)
                    ->whereYear('performed_at', now()->year)
                    ->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get recent activity.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recentActivity(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;

        $activities = [];

        // Recent students
        $recentStudents = Student::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(function ($student) {
                return [
                    'type' => 'student_created',
                    'title' => 'Novo aluno cadastrado',
                    'description' => $student->name,
                    'date' => $student->created_at,
                    'student_id' => $student->id,
                ];
            });

        // Recent workouts
        $recentWorkouts = Workout::with('student:id,name')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($workout) {
                return [
                    'type' => 'workout_created',
                    'title' => 'Novo treino criado',
                    'description' => "{$workout->name} - {$workout->student->name}",
                    'date' => $workout->created_at,
                    'workout_id' => $workout->id,
                    'student_id' => $workout->student_id,
                ];
            });

        // Recent goals
        $recentGoals = Goal::with('student:id,name')
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($goal) {
                return [
                    'type' => 'goal_completed',
                    'title' => 'Meta concluída',
                    'description' => "{$goal->title} - {$goal->student->name}",
                    'date' => $goal->completed_at,
                    'goal_id' => $goal->id,
                    'student_id' => $goal->student_id,
                ];
            });

        // Merge and sort all activities
        $activities = collect()
            ->concat($recentStudents)
            ->concat($recentWorkouts)
            ->concat($recentGoals)
            ->sortByDesc('date')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
