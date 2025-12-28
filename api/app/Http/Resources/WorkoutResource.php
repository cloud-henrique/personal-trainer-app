<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
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
            'student_id' => $this->student_id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'category_label' => $this->formatCategory(),
            'starts_at' => $this->starts_at?->format('d/m/Y'),
            'ends_at' => $this->ends_at?->format('d/m/Y'),
            'is_active' => (bool) $this->is_active,
            'total_exercises' => $this->when(
                $this->relationLoaded('exercises'),
                fn() => $this->exercises->count()
            ),
            'duration_days' => $this->calculateDurationDays(),
            'student' => new StudentResource($this->whenLoaded('student')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'exercises' => ExerciseResource::collection($this->whenLoaded('exercises')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Format the workout category to Portuguese label.
     */
    private function formatCategory(): string
    {
        return match($this->category) {
            'strength' => 'Força',
            'cardio' => 'Cardio',
            'flexibility' => 'Flexibilidade',
            'mixed' => 'Misto',
            default => $this->category,
        };
    }

    /**
     * Calculate the duration in days between starts_at and ends_at.
     */
    private function calculateDurationDays(): ?int
    {
        if (!$this->starts_at || !$this->ends_at) {
            return null;
        }

        return (int) $this->starts_at->diffInDays($this->ends_at);
    }
}
