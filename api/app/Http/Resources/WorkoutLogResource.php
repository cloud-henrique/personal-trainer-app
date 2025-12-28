<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workout_id' => $this->workout_id,
            'exercise_id' => $this->exercise_id,
            'student_id' => $this->student_id,
            'performed_at' => $this->performed_at?->format('d/m/Y'),
            'set_number' => (int) $this->set_number,
            'reps_completed' => (int) $this->reps_completed,
            'load_used' => $this->load_used ? (float) $this->load_used : null,
            'notes' => $this->notes,
            'total_volume' => $this->calculateTotalVolume(),
            'workout' => new WorkoutResource($this->whenLoaded('workout')),
            'exercise' => new ExerciseResource($this->whenLoaded('exercise')),
            'student' => new StudentResource($this->whenLoaded('student')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Calculate total volume (load × reps).
     */
    private function calculateTotalVolume(): ?float
    {
        if (!$this->load_used || !$this->reps_completed) {
            return null;
        }

        return round($this->load_used * $this->reps_completed, 2);
    }
}
