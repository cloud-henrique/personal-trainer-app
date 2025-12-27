import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { useLogin } from '../../services/authService';
import { useThemeStore } from '../../stores/themeStore';

export default function LoginScreen({ navigation }: any) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const { theme } = useThemeStore();
  const loginMutation = useLogin();

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Erro', 'Preencha todos os campos');
      return;
    }

    try {
      await loginMutation.mutateAsync({ email, password });
      // Navigation será tratada pelo App.tsx baseado em isAuthenticated
    } catch (error: any) {
      const message =
        error.response?.data?.message || 'Erro ao fazer login';
      Alert.alert('Erro', message);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      className="flex-1"
    >
      <ScrollView
        contentContainerStyle={{ flexGrow: 1 }}
        keyboardShouldPersistTaps="handled"
      >
        <View className="flex-1 bg-white">
          {/* Cover Image */}
          {theme.coverUrl && (
            <Image
              source={{ uri: theme.coverUrl }}
              className="w-full h-48"
              resizeMode="cover"
            />
          )}

          {/* Content */}
          <View className="flex-1 px-6 pt-8">
            {/* Logo */}
            {theme.logoUrl ? (
              <Image
                source={{ uri: theme.logoUrl }}
                className="w-32 h-32 self-center mb-8"
                resizeMode="contain"
              />
            ) : (
              <Text
                className="text-3xl font-bold text-center mb-8"
                style={{ color: theme.primaryColor }}
              >
                {theme.name}
              </Text>
            )}

            {/* Title */}
            <Text className="text-2xl font-bold text-gray-800 mb-2">
              Bem-vindo!
            </Text>
            <Text className="text-gray-600 mb-8">
              Entre com suas credenciais
            </Text>

            {/* Email Input */}
            <View className="mb-4">
              <Text className="text-gray-700 mb-2 font-medium">Email</Text>
              <TextInput
                className="border border-gray-300 rounded-lg px-4 py-3 text-base"
                placeholder="seu@email.com"
                value={email}
                onChangeText={setEmail}
                keyboardType="email-address"
                autoCapitalize="none"
                editable={!loginMutation.isPending}
                autoCorrect={false}
              />
            </View>

            {/* Password Input */}
            <View className="mb-6">
              <Text className="text-gray-700 mb-2 font-medium">Senha</Text>
              <TextInput
                className="border border-gray-300 rounded-lg px-4 py-3 text-base"
                placeholder="••••••••"
                value={password}
                onChangeText={setPassword}
                secureTextEntry={true}
                editable={!loginMutation.isPending}
                autoCorrect={false}
                autoCapitalize="none"
              />
            </View>

            {/* Login Button */}
            <TouchableOpacity
              className="rounded-lg py-4 items-center mb-4"
              style={{ backgroundColor: theme.primaryColor }}
              onPress={handleLogin}
              disabled={loginMutation.isPending}
            >
              {loginMutation.isPending ? (
                <ActivityIndicator color="white" />
              ) : (
                <Text className="text-white font-bold text-lg">Entrar</Text>
              )}
            </TouchableOpacity>

            {/* Register Link */}
            <TouchableOpacity
              onPress={() => navigation.navigate('Register')}
              disabled={loginMutation.isPending}
            >
              <Text className="text-center text-gray-600">
                Não tem uma conta?{' '}
                <Text
                  className="font-bold"
                  style={{ color: theme.primaryColor }}
                >
                  Cadastre-se
                </Text>
              </Text>
            </TouchableOpacity>

            {/* Test Credentials (Dev only) */}
            {__DEV__ && (
              <View className="mt-8 p-4 bg-gray-100 rounded-lg">
                <Text className="text-xs text-gray-600 font-bold mb-2">
                  Credenciais de Teste:
                </Text>
                <Text className="text-xs text-gray-600">
                  carlos@fitnesspro.com / password123
                </Text>
                <Text className="text-xs text-gray-600">
                  ana@personalstrong.com / password123
                </Text>
              </View>
            )}
          </View>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}
