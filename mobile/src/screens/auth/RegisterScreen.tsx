import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { useRegister } from '../../services/authService';

export default function RegisterScreen({ navigation }: any) {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [slug, setSlug] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const registerMutation = useRegister();

  const handleRegister = async () => {
    if (!name || !email || !slug || !password || !passwordConfirmation) {
      Alert.alert('Erro', 'Preencha todos os campos');
      return;
    }

    if (password !== passwordConfirmation) {
      Alert.alert('Erro', 'As senhas não coincidem');
      return;
    }

    try {
      await registerMutation.mutateAsync({
        name,
        email,
        slug,
        password,
        password_confirmation: passwordConfirmation,
      });
      // Navigation será tratada pelo App.tsx baseado em isAuthenticated
    } catch (error: any) {
      const errors = error.response?.data?.errors;
      const message = error.response?.data?.message || 'Erro ao cadastrar';

      if (errors) {
        const errorMessages = Object.values(errors).flat().join('\n');
        Alert.alert('Erro de Validação', errorMessages);
      } else {
        Alert.alert('Erro', message);
      }
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
        <View className="flex-1 bg-white px-6 pt-12">
          {/* Title */}
          <Text className="text-3xl font-bold text-gray-800 mb-2">
            Crie sua Conta
          </Text>
          <Text className="text-gray-600 mb-8">
            Configure seu personal trainer
          </Text>

          {/* Name Input */}
          <View className="mb-4">
            <Text className="text-gray-700 mb-2 font-medium">Nome</Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-base"
              placeholder="Seu nome completo"
              value={name}
              onChangeText={setName}
              editable={!registerMutation.isPending}
              autoCorrect={false}
            />
          </View>

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
              editable={!registerMutation.isPending}
              autoCorrect={false}
            />
          </View>

          {/* Slug Input */}
          <View className="mb-4">
            <Text className="text-gray-700 mb-2 font-medium">
              Identificador Único (slug)
            </Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-base"
              placeholder="seu-personal"
              value={slug}
              onChangeText={(text) => setSlug(text.toLowerCase())}
              autoCapitalize="none"
              editable={!registerMutation.isPending}
              autoCorrect={false}
            />
            <Text className="text-gray-500 text-xs mt-1">
              Este será seu identificador único (ex: seu-personal)
            </Text>
          </View>

          {/* Password Input */}
          <View className="mb-4">
            <Text className="text-gray-700 mb-2 font-medium">Senha</Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-base"
              placeholder="••••••••"
              value={password}
              onChangeText={setPassword}
              secureTextEntry={true}
              editable={!registerMutation.isPending}
              autoCorrect={false}
              autoCapitalize="none"
            />
          </View>

          {/* Password Confirmation Input */}
          <View className="mb-6">
            <Text className="text-gray-700 mb-2 font-medium">
              Confirme a Senha
            </Text>
            <TextInput
              className="border border-gray-300 rounded-lg px-4 py-3 text-base"
              placeholder="••••••••"
              value={passwordConfirmation}
              onChangeText={setPasswordConfirmation}
              secureTextEntry={true}
              editable={!registerMutation.isPending}
              autoCorrect={false}
              autoCapitalize="none"
            />
          </View>

          {/* Register Button */}
          <TouchableOpacity
            className="bg-blue-500 rounded-lg py-4 items-center mb-4"
            onPress={handleRegister}
            disabled={registerMutation.isPending}
          >
            {registerMutation.isPending ? (
              <ActivityIndicator color="white" />
            ) : (
              <Text className="text-white font-bold text-lg">
                Criar Conta
              </Text>
            )}
          </TouchableOpacity>

          {/* Login Link */}
          <TouchableOpacity
            onPress={() => navigation.navigate('Login')}
            disabled={registerMutation.isPending}
          >
            <Text className="text-center text-gray-600">
              Já tem uma conta?{' '}
              <Text className="text-blue-500 font-bold">Entre</Text>
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}
