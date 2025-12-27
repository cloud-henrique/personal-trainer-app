import axios from 'axios';
import { useAuthStore } from '../stores/authStore';

// API base URL - alterar para produção
const API_BASE_URL = 'http://localhost:8000/api/v1';

export const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

// Interceptor para adicionar token em todas as requests
api.interceptors.request.use(
  config => {
    const token = useAuthStore.getState().token;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);

// Interceptor para tratar erros globais
api.interceptors.response.use(
  response => response,
  error => {
    // Se token expirado (401), fazer logout
    if (error.response?.status === 401) {
      useAuthStore.getState().logout();
    }
    return Promise.reject(error);
  }
);

// Tipos de resposta da API
export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  errors?: Record<string, string[]>;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  slug: string;
}

export interface TenantData {
  id: string;
  name: string;
  slug: string;
  primary_color: string;
  logo_url: string | null;
  cover_url: string | null;
  email: string;
  plan: 'free' | 'basic' | 'premium';
}

export interface AuthResponse {
  token: string;
  user: {
    id: number;
    tenant_id: string;
    name: string;
    email: string;
    phone: string | null;
    avatar_url: string | null;
    role: 'admin' | 'trainer' | 'student';
    is_active: boolean;
  };
  tenant: TenantData;
}
