<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'avatar_url' => $this->avatar_url,
            'avatar_full_url' => $this->getAvatarFullUrl(),
            'role' => $this->role,
            'role_label' => $this->formatRole(),
            'is_active' => (bool) $this->is_active,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Format the user role to Portuguese label.
     */
    private function formatRole(): string
    {
        return match($this->role) {
            'admin' => 'Administrador',
            'trainer' => 'Personal Trainer',
            'student' => 'Aluno',
            default => $this->role,
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
