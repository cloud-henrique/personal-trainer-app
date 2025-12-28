<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
            'order' => (int) $this->order,
            'name' => $this->name,
            'muscle_group' => $this->muscle_group,
            'description' => $this->description,
            'video_url' => $this->video_url,
            'video_embed_url' => $this->getVideoEmbedUrl(),
            'sets' => (int) $this->sets,
            'reps' => $this->reps,
            'rest' => $this->rest,
            'load' => $this->load,
            'tempo' => $this->tempo,
            'notes' => $this->notes,
            'workout' => new WorkoutResource($this->whenLoaded('workout')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get the embeddable video URL.
     * Converts YouTube watch URLs to embed format.
     */
    private function getVideoEmbedUrl(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // If it's a YouTube URL, convert to embed format
        if (str_contains($this->video_url, 'youtube.com/watch?v=')) {
            preg_match('/[?&]v=([^&]+)/', $this->video_url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // If it's already an embed URL or another format, return as is
        return $this->video_url;
    }
}
