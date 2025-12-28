<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * StudentController
 *
 * Manages student (client) CRUD operations.
 * All queries are automatically filtered by tenant_id via BelongsToTenant trait.
 */
class StudentController extends Controller
{
    /**
     * List all students for the authenticated tenant.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $students = Student::query()
            ->with(['trainer:id,name'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => StudentResource::collection($students),
        ]);
    }

    /**
     * Create a new student.
     *
     * @param StoreStudentRequest $request
     * @return JsonResponse
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            $student = Student::create($request->validated());

            Log::channel('students')->info('Student created', [
                'student_id' => $student->id,
                'tenant_id' => auth()->user()->tenant_id,
            ]);

            return response()->json([
                'success' => true,
                'data' => new StudentResource($student->load('trainer:id,name')),
                'message' => 'Aluno criado com sucesso',
            ], 201);

        } catch (\Exception $e) {
            Log::channel('students')->error('Student creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar aluno',
            ], 500);
        }
    }

    /**
     * Show student details.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function show(Student $student): JsonResponse
    {
        $student->load([
            'trainer:id,name',
            'measurements' => fn($q) => $q->latest('measured_at')->limit(5),
            'goals' => fn($q) => $q->where('status', 'active'),
        ]);

        return response()->json([
            'success' => true,
            'data' => new StudentResource($student),
        ]);
    }

    /**
     * Update student.
     *
     * @param UpdateStudentRequest $request
     * @param Student $student
     * @return JsonResponse
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        try {
            $student->update($request->validated());

            Log::channel('students')->info('Student updated', [
                'student_id' => $student->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => new StudentResource($student->fresh()->load('trainer:id,name')),
                'message' => 'Aluno atualizado com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('students')->error('Student update failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar aluno',
            ], 500);
        }
    }

    /**
     * Delete student (soft delete).
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $student->delete();

            Log::channel('students')->info('Student deleted', [
                'student_id' => $student->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aluno excluído com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('students')->error('Student deletion failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir aluno',
            ], 500);
        }
    }
}
