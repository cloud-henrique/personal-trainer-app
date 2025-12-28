<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date?->format('d/m/Y'),
            'age' => $this->birth_date?->age,
            'avatar_url' => $this->avatar_url,
            'avatar_full_url' => $this->getAvatarFullUrl(),
            'gender' => $this->gender,
            'gender_label' => $this->formatGender(),
            'height' => $this->height ? (float) $this->height : null,
            'medical_conditions' => $this->medical_conditions,
            'notes' => $this->notes,
            'is_active' => (bool) $this->is_active,
            'active_workouts_count' => $this->when(
                $this->relationLoaded('workouts'),
                fn() => $this->workouts->where('is_active', true)->count()
            ),
            'active_goals_count' => $this->when(
                $this->relationLoaded('goals'),
                fn() => $this->goals->where('status', 'active')->count()
            ),
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'latest_measurement' => new StudentMeasurementResource($this->whenLoaded('latestMeasurement')),
            'measurements' => StudentMeasurementResource::collection($this->whenLoaded('measurements')),
            'workouts' => WorkoutResource::collection($this->whenLoaded('workouts')),
            'goals' => GoalResource::collection($this->whenLoaded('goals')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Format the gender to Portuguese label.
     */
    private function formatGender(): ?string
    {
        if (!$this->gender) {
            return null;
        }

        return match($this->gender) {
            'male' => 'Masculino',
            'female' => 'Feminino',
            'other' => 'Outro',
            default => $this->gender,
        };
    }

    /**
     * Get the full URL for the avatar.
     */
    private function getAvatarFullUrl(): ?string
    {
        if (!$this->avatar_url) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
            return $this->avatar_url;
        }

        // Convert relative path to absolute URL
        return url($this->avatar_url);
    }
}
