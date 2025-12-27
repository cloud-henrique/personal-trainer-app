import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import AsyncStorage from '@react-native-async-storage/async-storage';

export interface TenantTheme {
  primaryColor: string;
  logoUrl: string | null;
  coverUrl: string | null;
  name: string;
}

interface ThemeState {
  theme: TenantTheme;
  setTheme: (theme: TenantTheme) => void;
  clearTheme: () => void;
}

const defaultTheme: TenantTheme = {
  primaryColor: '#3B82F6',
  logoUrl: null,
  coverUrl: null,
  name: 'Personal Trainer',
};

export const useThemeStore = create<ThemeState>()(
  persist(
    (set) => ({
      theme: defaultTheme,
      setTheme: (theme) => set({ theme }),
      clearTheme: () => set({ theme: defaultTheme }),
    }),
    {
      name: 'tenant-theme-storage',
      storage: createJSONStorage(() => AsyncStorage),
    }
  )
);
