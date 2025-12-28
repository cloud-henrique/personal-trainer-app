export interface Student {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  birth_date: string | null; // formato: dd/mm/yyyy
  age: number | null;
  avatar_url: string | null;
  avatar_full_url: string | null;
  gender: 'male' | 'female' | 'other' | null;
  gender_label: string | null;
  height: number | null;
  medical_conditions: string | null;
  notes: string | null;
  is_active: boolean;
  trainer?: {
    id: number;
    name: string;
  };
  latest_measurement?: {
    id: number;
    weight: number | null;
    measured_at: string;
  };
  active_goals_count?: number;
  active_workouts_count?: number;
  created_at: string;
  updated_at: string;
}

export interface StudentFormData {
  name: string;
  email: string;
  phone?: string | null;
  birth_date?: Date | null;
  gender?: 'male' | 'female' | 'other' | null;
  height?: number | null;
  medical_conditions?: string | null;
  notes?: string | null;
  is_active?: boolean;
}

export interface StudentListParams {
  search?: string;
  is_active?: boolean | null;
  page?: number;
  per_page?: number;
}

export interface PaginatedStudentsResponse {
  data: Student[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}
