<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentMeasurementResource extends JsonResource
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
            'measured_at' => $this->measured_at?->format('d/m/Y'),
            'notes' => $this->notes,
            'measurements' => [
                'weight' => $this->weight ? (float) $this->weight : null,
                'body_fat' => $this->body_fat ? (float) $this->body_fat : null,
                'muscle_mass' => $this->muscle_mass ? (float) $this->muscle_mass : null,
                'circumferences' => [
                    'chest' => $this->chest ? (float) $this->chest : null,
                    'waist' => $this->waist ? (float) $this->waist : null,
                    'hips' => $this->hips ? (float) $this->hips : null,
                    'right_arm' => $this->right_arm ? (float) $this->right_arm : null,
                    'left_arm' => $this->left_arm ? (float) $this->left_arm : null,
                    'right_thigh' => $this->right_thigh ? (float) $this->right_thigh : null,
                    'left_thigh' => $this->left_thigh ? (float) $this->left_thigh : null,
                    'right_calf' => $this->right_calf ? (float) $this->right_calf : null,
                    'left_calf' => $this->left_calf ? (float) $this->left_calf : null,
                ],
            ],
            'student' => new StudentResource($this->whenLoaded('student')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
