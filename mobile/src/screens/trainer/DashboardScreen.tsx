import React from 'react';
import { View, Text, TouchableOpacity, SafeAreaView } from 'react-native';
import { useAuthStore } from '../../stores/authStore';
import { useThemeStore } from '../../stores/themeStore';
import { useLogout } from '../../services/authService';

export default function DashboardScreen() {
  const { user } = useAuthStore();
  const { theme } = useThemeStore();
  const logoutMutation = useLogout();

  const handleLogout = async () => {
    await logoutMutation.mutateAsync();
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      <View className="flex-1 p-6">
        {/* Header */}
        <View className="mb-8">
          <Text
            className="text-3xl font-bold mb-2"
            style={{ color: theme.primaryColor }}
          >
            Olá, {user?.name}!
          </Text>
          <Text className="text-gray-600 text-lg">
            Bem-vindo ao {theme.name}
          </Text>
        </View>

        {/* Stats Cards */}
        <View className="space-y-4">
          <View className="bg-gray-100 rounded-lg p-6">
            <Text className="text-gray-600 mb-2">Total de Alunos</Text>
            <Text
              className="text-4xl font-bold"
              style={{ color: theme.primaryColor }}
            >
              0
            </Text>
          </View>

          <View className="bg-gray-100 rounded-lg p-6">
            <Text className="text-gray-600 mb-2">Treinos Ativos</Text>
            <Text
              className="text-4xl font-bold"
              style={{ color: theme.primaryColor }}
            >
              0
            </Text>
          </View>
        </View>

        {/* Logout Button */}
        <TouchableOpacity
          className="mt-auto bg-red-500 rounded-lg py-4 items-center"
          onPress={handleLogout}
          disabled={logoutMutation.isPending}
        >
          <Text className="text-white font-bold text-lg">Sair</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}
