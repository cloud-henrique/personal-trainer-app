import React, { useState, useCallback } from 'react';
import {
  View,
  Text,
  TextInput,
  FlatList,
  RefreshControl,
  ActivityIndicator,
  TouchableOpacity,
  SafeAreaView,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useStudents } from '../../services/studentService';
import StudentCard from '../../components/StudentCard';
import EmptyState from '../../components/EmptyState';
import type { Student } from '../../types/student';
import { useDebounce } from '../../hooks/useDebounce';

type NavigationProp = NativeStackNavigationProp<any>;

const StudentListScreen: React.FC = () => {
  const navigation = useNavigation<NavigationProp>();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState<boolean | null>(null);

  // Debounce search para evitar muitas requisições
  const debouncedSearch = useDebounce(search, 500);

  // Query com infinite scroll
  const { data, fetchNextPage, hasNextPage, isFetchingNextPage, refetch, isLoading, isRefetching } = useStudents({
    search: debouncedSearch || undefined,
    is_active: statusFilter ?? undefined,
  });

  // Flatten all pages into single array
  const students: Student[] = data?.pages.flatMap(page => page.data) ?? [];

  const handleLoadMore = () => {
    if (hasNextPage && !isFetchingNextPage) {
      fetchNextPage();
    }
  };

  const handleStudentPress = (studentId: number) => {
    navigation.navigate('StudentDetail', { studentId });
  };

  const handleCreateStudent = () => {
    navigation.navigate('StudentForm', { mode: 'create' });
  };

  const renderFooter = () => {
    if (!isFetchingNextPage) return null;
    return (
      <View className='py-4'>
        <ActivityIndicator size='small' color='#3B82F6' />
      </View>
    );
  };

  const renderEmptyState = () => {
    if (isLoading) return null;

    if (debouncedSearch || statusFilter !== null) {
      return <EmptyState icon='🔍' title='Nenhum aluno encontrado' description='Tente ajustar os filtros de busca' />;
    }

    return (
      <EmptyState
        icon='👥'
        title='Nenhum aluno cadastrado'
        description='Comece adicionando seu primeiro aluno'
        actionLabel='Adicionar Aluno'
        onAction={handleCreateStudent}
      />
    );
  };

  const renderFilterButton = (label: string, value: boolean | null) => {
    const isActive = statusFilter === value;
    return (
      <TouchableOpacity
        onPress={() => setStatusFilter(value)}
        className={`px-4 py-2 rounded-full mr-2 ${isActive ? 'bg-blue-500' : 'bg-gray-200'}`}
        activeOpacity={0.7}
      >
        <Text className={`font-medium ${isActive ? 'text-white' : 'text-gray-700'}`}>{label}</Text>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView className='flex-1 bg-gray-50'>
      {/* Header */}
      <View className='bg-white px-4 py-3 border-b border-gray-200'>
        <View className='flex-row items-center justify-between mb-3'>
          <Text className='text-2xl font-bold text-gray-900'>Meus Alunos</Text>
          <TouchableOpacity
            onPress={handleCreateStudent}
            className='bg-blue-500 w-10 h-10 rounded-full items-center justify-center'
            activeOpacity={0.8}
          >
            <Text className='text-white text-2xl font-light'>+</Text>
          </TouchableOpacity>
        </View>

        {/* Search Bar */}
        <View className='bg-gray-100 rounded-lg px-3 py-2 flex-row items-center mb-3'>
          <Text className='text-gray-400 mr-2'>🔍</Text>
          <TextInput
            value={search}
            onChangeText={setSearch}
            placeholder='Buscar por nome ou email...'
            placeholderTextColor='#9CA3AF'
            className='flex-1 text-gray-900'
          />
          {search.length > 0 && (
            <TouchableOpacity onPress={() => setSearch('')}>
              <Text className='text-gray-400'>✕</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Status Filters */}
        <View className='flex-row'>
          {renderFilterButton('Todos', null)}
          {renderFilterButton('Ativos', true)}
          {renderFilterButton('Inativos', false)}
        </View>
      </View>

      {/* Student List */}
      {isLoading && !isRefetching ? (
        <View className='flex-1 items-center justify-center'>
          <ActivityIndicator size='large' color='#3B82F6' />
        </View>
      ) : (
        <FlatList
          data={students}
          renderItem={({ item }) => <StudentCard student={item} onPress={() => handleStudentPress(item.id)} />}
          // keyExtractor={(item, index) => index}
          contentContainerStyle={{
            padding: 16,
            flexGrow: 1,
          }}
          ListEmptyComponent={renderEmptyState}
          ListFooterComponent={renderFooter}
          onEndReached={handleLoadMore}
          onEndReachedThreshold={0.5}
          refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor='#3B82F6' />}
        />
      )}
    </SafeAreaView>
  );
};

export default StudentListScreen;
