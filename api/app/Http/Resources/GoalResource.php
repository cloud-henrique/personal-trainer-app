<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->formatType(),
            'target_value' => $this->target_value ? (float) $this->target_value : null,
            'current_value' => $this->current_value ? (float) $this->current_value : null,
            'unit' => $this->unit,
            'starts_at' => $this->starts_at?->format('d/m/Y'),
            'target_date' => $this->target_date?->format('d/m/Y'),
            'completed_at' => $this->completed_at?->format('d/m/Y'),
            'status' => $this->status,
            'status_label' => $this->formatStatus(),
            'progress_percentage' => $this->calculateProgress(),
            'days_remaining' => $this->calculateDaysRemaining(),
            'student' => new StudentResource($this->whenLoaded('student')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Format the goal type to Portuguese label.
     */
    private function formatType(): string
    {
        return match($this->type) {
            'weight_loss' => 'Perda de Peso',
            'muscle_gain' => 'Ganho de Massa',
            'performance' => 'Performance',
            'other' => 'Outro',
            default => $this->type,
        };
    }

    /**
     * Format the goal status to Portuguese label.
     */
    private function formatStatus(): string
    {
        return match($this->status) {
            'active' => 'Ativa',
            'completed' => 'Concluída',
            'cancelled' => 'Cancelada',
            default => $this->status,
        };
    }

    /**
     * Calculate the progress percentage based on current and target values.
     */
    private function calculateProgress(): ?float
    {
        if (!$this->target_value || !$this->current_value) {
            return null;
        }

        $progress = ($this->current_value / $this->target_value) * 100;
        return round($progress, 2);
    }

    /**
     * Calculate days remaining until target date.
     * Returns negative number if overdue.
     */
    private function calculateDaysRemaining(): ?int
    {
        if (!$this->target_date) {
            return null;
        }

        return (int) now()->diffInDays($this->target_date, false);
    }
}
