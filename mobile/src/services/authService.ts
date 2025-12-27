import { useMutation, useQuery } from '@tanstack/react-query';
import { api, ApiResponse, LoginCredentials, RegisterData, AuthResponse } from './api';
import { useAuthStore } from '../stores/authStore';
import { useThemeStore } from '../stores/themeStore';

// Login mutation
export const useLogin = () => {
  const { setAuth } = useAuthStore();
  const { setTheme } = useThemeStore();

  return useMutation({
    mutationFn: async (credentials: LoginCredentials) => {
      const response = await api.post<ApiResponse<AuthResponse>>(
        '/auth/login',
        credentials
      );
      return response.data.data!;
    },
    onSuccess: (data) => {
      // Salvar autenticação
      setAuth(data.token, data.user);

      // Salvar tema do tenant
      setTheme({
        primaryColor: data.tenant.primary_color,
        logoUrl: data.tenant.logo_url,
        coverUrl: data.tenant.cover_url,
        name: data.tenant.name,
      });
    },
  });
};

// Register mutation
export const useRegister = () => {
  const { setAuth } = useAuthStore();
  const { setTheme } = useThemeStore();

  return useMutation({
    mutationFn: async (data: RegisterData) => {
      const response = await api.post<ApiResponse<AuthResponse>>(
        '/auth/register',
        data
      );
      return response.data.data!;
    },
    onSuccess: (data) => {
      // Salvar autenticação
      setAuth(data.token, data.user);

      // Salvar tema do tenant
      setTheme({
        primaryColor: data.tenant.primary_color,
        logoUrl: data.tenant.logo_url,
        coverUrl: data.tenant.cover_url,
        name: data.tenant.name,
      });
    },
  });
};

// Logout mutation
export const useLogout = () => {
  const { logout } = useAuthStore();
  const { clearTheme } = useThemeStore();

  return useMutation({
    mutationFn: async () => {
      await api.post('/auth/logout');
    },
    onSuccess: () => {
      logout();
      clearTheme();
    },
  });
};

// Get current user
export const useMe = () => {
  const { token } = useAuthStore();

  return useQuery({
    queryKey: ['me'],
    queryFn: async () => {
      const response = await api.get<ApiResponse<{ user: any; tenant: any }>>(
        '/auth/me'
      );
      return response.data.data!;
    },
    enabled: !!token,
  });
};
