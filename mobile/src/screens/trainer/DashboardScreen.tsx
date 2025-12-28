import React from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { useAuthStore } from '../../stores/authStore';
import { useThemeStore } from '../../stores/themeStore';
import { useLogout } from '../../services/authService';

type NavigationProp = NativeStackNavigationProp<any>;

export default function DashboardScreen() {
  const navigation = useNavigation<NavigationProp>();
  const { user } = useAuthStore();
  const { theme } = useThemeStore();
  const logoutMutation = useLogout();

  const handleLogout = async () => {
    await logoutMutation.mutateAsync();
  };

  return (
    <SafeAreaProvider className='flex-1 bg-white'>
      <View className='flex-1 p-6'>
        {/* Header */}
        <View className='mb-8'>
          <Text className='text-3xl font-bold mb-2' style={{ color: theme.primaryColor }}>
            Olá, {user?.name}!
          </Text>
          <Text className='text-gray-600 text-lg'>Bem-vindo ao {theme.name}</Text>
        </View>

        {/* Quick Actions */}
        <View className='mb-6'>
          <Text className='text-lg font-semibold text-gray-900 mb-3'>Acesso Rápido</Text>

          <TouchableOpacity
            className='rounded-lg p-4 mb-3 flex-row items-center justify-between'
            style={{ backgroundColor: theme.primaryColor }}
            onPress={() => navigation.navigate('StudentList')}
            activeOpacity={0.8}
          >
            <View>
              <Text className='text-white font-semibold text-lg'>Meus Alunos</Text>
              <Text className='text-white opacity-80 text-sm'>Gerenciar alunos cadastrados</Text>
            </View>
            <Text className='text-white text-2xl'>›</Text>
          </TouchableOpacity>
        </View>

        {/* Stats Cards */}
        <View className='space-y-4'>
          <View className='bg-gray-100 rounded-lg p-6'>
            <Text className='text-gray-600 mb-2'>Total de Alunos</Text>
            <Text className='text-4xl font-bold' style={{ color: theme.primaryColor }}>
              0
            </Text>
          </View>

          <View className='bg-gray-100 rounded-lg p-6'>
            <Text className='text-gray-600 mb-2'>Treinos Ativos</Text>
            <Text className='text-4xl font-bold' style={{ color: theme.primaryColor }}>
              0
            </Text>
          </View>
        </View>

        {/* Logout Button */}
        <TouchableOpacity
          className='mt-auto bg-red-500 rounded-lg py-4 items-center'
          onPress={handleLogout}
          disabled={logoutMutation.isPending}
        >
          <Text className='text-white font-bold text-lg'>Sair</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaProvider>
  );
}
