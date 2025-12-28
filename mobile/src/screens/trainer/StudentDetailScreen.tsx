import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  SafeAreaView,
} from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useStudent, useDeleteStudent } from '../../services/studentService';
import Avatar from '../../components/Avatar';

type RouteParams = {
  StudentDetail: {
    studentId: number;
  };
};

type NavigationProp = NativeStackNavigationProp<any>;

const StudentDetailScreen: React.FC = () => {
  const navigation = useNavigation<NavigationProp>();
  const route = useRoute<RouteProp<RouteParams, 'StudentDetail'>>();
  const { studentId } = route.params;

  const [showMenu, setShowMenu] = useState(false);

  const { data: student, isLoading, isError, refetch } = useStudent(studentId);
  const deleteMutation = useDeleteStudent();

  const handleEdit = () => {
    setShowMenu(false);
    navigation.navigate('StudentForm', { studentId, mode: 'edit' });
  };

  const handleDelete = () => {
    setShowMenu(false);
    Alert.alert(
      'Excluir Aluno',
      `Tem certeza que deseja excluir ${student?.name}? Esta ação não pode ser desfeita.`,
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Excluir',
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteMutation.mutateAsync(studentId);
              navigation.goBack();
            } catch (error: any) {
              const message = error.response?.data?.message || 'Erro ao excluir aluno';
              Alert.alert('Erro', message);
            }
          },
        },
      ]
    );
  };

  if (isLoading) {
    return (
      <SafeAreaView className='flex-1 bg-white items-center justify-center'>
        <ActivityIndicator size='large' color='#3B82F6' />
      </SafeAreaView>
    );
  }

  if (isError || !student) {
    return (
      <SafeAreaView className='flex-1 bg-white items-center justify-center px-8'>
        <Text className='text-xl font-semibold text-gray-900 text-center mb-2'>
          Erro ao carregar aluno
        </Text>
        <Text className='text-gray-500 text-center mb-6'>
          Não foi possível carregar as informações do aluno
        </Text>
        <TouchableOpacity
          onPress={() => refetch()}
          className='bg-blue-500 px-6 py-3 rounded-lg'
        >
          <Text className='text-white font-semibold'>Tentar novamente</Text>
        </TouchableOpacity>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView className='flex-1 bg-white'>
      {/* Header */}
      <View className='px-4 py-3 border-b border-gray-200 flex-row items-center justify-between'>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text className='text-blue-500 text-base'>‹ Voltar</Text>
        </TouchableOpacity>
        <Text className='text-lg font-semibold'>Detalhes do Aluno</Text>
        <TouchableOpacity onPress={() => setShowMenu(!showMenu)}>
          <Text className='text-gray-600 text-2xl'>⋮</Text>
        </TouchableOpacity>
      </View>

      {/* Menu Dropdown (Simples) */}
      {showMenu && (
        <View className='absolute top-16 right-4 bg-white rounded-lg shadow-lg border border-gray-200 z-10'>
          <TouchableOpacity
            onPress={handleEdit}
            className='px-4 py-3 border-b border-gray-200'
          >
            <Text className='text-blue-500 font-medium'>Editar</Text>
          </TouchableOpacity>
          <TouchableOpacity onPress={handleDelete} className='px-4 py-3'>
            <Text className='text-red-500 font-medium'>Excluir</Text>
          </TouchableOpacity>
        </View>
      )}

      <ScrollView className='flex-1'>
        {/* Hero Section */}
        <View className='items-center py-6 bg-gray-50'>
          <Avatar name={student.name} size={80} />
          <Text className='text-2xl font-bold text-gray-900 mt-4'>
            {student.name}
          </Text>
          <Text className='text-gray-500 mt-1'>{student.email}</Text>
          {student.phone && (
            <Text className='text-gray-500 mt-0.5'>{student.phone}</Text>
          )}
          {student.age && (
            <Text className='text-gray-500 mt-0.5'>{student.age} anos</Text>
          )}
          <View
            className={`mt-3 px-3 py-1 rounded-full ${
              student.is_active ? 'bg-green-100' : 'bg-gray-100'
            }`}
          >
            <Text
              className={`text-sm font-medium ${
                student.is_active ? 'text-green-700' : 'text-gray-500'
              }`}
            >
              {student.is_active ? 'Ativo' : 'Inativo'}
            </Text>
          </View>
        </View>

        {/* Informações Pessoais */}
        <View className='px-4 py-4'>
          <Text className='text-lg font-semibold text-gray-900 mb-3'>
            Informações Pessoais
          </Text>

          {/* Altura */}
          {student.height && (
            <View className='flex-row justify-between py-3 border-b border-gray-200'>
              <Text className='text-gray-600'>Altura</Text>
              <Text className='text-gray-900 font-medium'>
                {student.height} cm
              </Text>
            </View>
          )}

          {/* Gênero */}
          {student.gender_label && (
            <View className='flex-row justify-between py-3 border-b border-gray-200'>
              <Text className='text-gray-600'>Gênero</Text>
              <Text className='text-gray-900 font-medium'>
                {student.gender_label}
              </Text>
            </View>
          )}

          {/* Data de Nascimento */}
          {student.birth_date && (
            <View className='flex-row justify-between py-3 border-b border-gray-200'>
              <Text className='text-gray-600'>Data de Nascimento</Text>
              <Text className='text-gray-900 font-medium'>
                {student.birth_date}
                {student.age && ` (${student.age} anos)`}
              </Text>
            </View>
          )}

          {/* Condições Médicas */}
          {student.medical_conditions && (
            <View className='py-3 border-b border-gray-200'>
              <Text className='text-gray-600 mb-2'>Condições Médicas</Text>
              <Text className='text-gray-900'>{student.medical_conditions}</Text>
            </View>
          )}

          {/* Notas */}
          {student.notes && (
            <View className='py-3'>
              <Text className='text-gray-600 mb-2'>Notas</Text>
              <Text className='text-gray-900'>{student.notes}</Text>
            </View>
          )}

          {/* Trainer */}
          {student.trainer && (
            <View className='flex-row justify-between py-3 border-t border-gray-200 mt-2'>
              <Text className='text-gray-600'>Personal Trainer</Text>
              <Text className='text-gray-900 font-medium'>
                {student.trainer.name}
              </Text>
            </View>
          )}
        </View>

        {/* Stats (se disponíveis) */}
        {(student.active_workouts_count !== undefined ||
          student.active_goals_count !== undefined) && (
          <View className='px-4 py-4 bg-gray-50'>
            <Text className='text-lg font-semibold text-gray-900 mb-3'>
              Resumo
            </Text>
            <View className='flex-row'>
              {student.active_workouts_count !== undefined && (
                <View className='flex-1 bg-white rounded-lg p-4 mr-2'>
                  <Text className='text-2xl font-bold text-blue-500'>
                    {student.active_workouts_count}
                  </Text>
                  <Text className='text-gray-600 mt-1'>Treinos Ativos</Text>
                </View>
              )}
              {student.active_goals_count !== undefined && (
                <View className='flex-1 bg-white rounded-lg p-4 ml-2'>
                  <Text className='text-2xl font-bold text-green-500'>
                    {student.active_goals_count}
                  </Text>
                  <Text className='text-gray-600 mt-1'>Metas Ativas</Text>
                </View>
              )}
            </View>
          </View>
        )}
      </ScrollView>

      {/* Botão Editar (Bottom) */}
      <View className='px-4 py-3 border-t border-gray-200'>
        <TouchableOpacity
          onPress={handleEdit}
          className='bg-blue-500 py-3 rounded-lg items-center'
          activeOpacity={0.8}
        >
          <Text className='text-white font-semibold text-base'>Editar Aluno</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
};

export default StudentDetailScreen;
