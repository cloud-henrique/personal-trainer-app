import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Switch,
  Platform,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import DateTimePicker from '@react-native-community/datetimepicker';
import { Picker } from '@react-native-picker/picker';
import { useCreateStudent, useUpdateStudent, useStudent, parseBRDate } from '../../services/studentService';
import type { StudentFormData } from '../../types/student';

// Schema de validação
const studentSchema = z.object({
  name: z.string().min(3, 'Mínimo 3 caracteres').max(255, 'Máximo 255 caracteres'),
  email: z.string().email('Email inválido'),
  phone: z.string().nullable().optional(),
  birth_date: z.date().nullable().optional(),
  gender: z.enum(['male', 'female', 'other']).nullable().optional(),
  height: z.number().min(0).max(999.99).nullable().optional(),
  medical_conditions: z.string().nullable().optional(),
  notes: z.string().nullable().optional(),
  is_active: z.boolean().optional(),
});

type StudentFormValues = z.infer<typeof studentSchema>;

type RouteParams = {
  StudentForm: {
    studentId?: number;
    mode: 'create' | 'edit';
  };
};

type NavigationProp = NativeStackNavigationProp<any>;

const StudentFormScreen: React.FC = () => {
  const navigation = useNavigation<NavigationProp>();
  const route = useRoute<RouteProp<RouteParams, 'StudentForm'>>();
  const { studentId, mode } = route.params;

  const [showDatePicker, setShowDatePicker] = useState(false);

  // Queries e mutations
  const { data: student, isLoading: isLoadingStudent } = useStudent(studentId);
  const createMutation = useCreateStudent();
  const updateMutation = useUpdateStudent(studentId!);

  // Form
  const {
    control,
    handleSubmit,
    formState: { errors },
    setValue,
    watch,
  } = useForm<StudentFormValues>({
    resolver: zodResolver(studentSchema),
    defaultValues: {
      name: '',
      email: '',
      phone: null,
      birth_date: null,
      gender: null,
      height: null,
      medical_conditions: null,
      notes: null,
      is_active: true,
    },
  });

  // Preenche form no modo edit
  useEffect(() => {
    if (mode === 'edit' && student) {
      setValue('name', student.name);
      setValue('email', student.email);
      setValue('phone', student.phone || null);
      setValue('birth_date', student.birth_date ? parseBRDate(student.birth_date) : null);
      setValue('gender', student.gender);
      setValue('height', student.height);
      setValue('medical_conditions', student.medical_conditions || null);
      setValue('notes', student.notes || null);
      setValue('is_active', student.is_active);
    }
  }, [student, mode, setValue]);

  const onSubmit = async (data: StudentFormValues) => {
    try {
      // Converter height de string para number se necessário
      const formData: StudentFormData = {
        ...data,
        height: data.height ? Number(data.height) : null,
      };

      if (mode === 'create') {
        await createMutation.mutateAsync(formData);
        Alert.alert('Sucesso', 'Aluno criado com sucesso!');
        navigation.goBack();
      } else {
        await updateMutation.mutateAsync(formData);
        Alert.alert('Sucesso', 'Aluno atualizado com sucesso!');
        navigation.goBack();
      }
    } catch (error: any) {
      const message = error.response?.data?.message || 'Erro ao salvar aluno';
      Alert.alert('Erro', message);
    }
  };

  const birthDate = watch('birth_date');

  const isLoading = createMutation.isPending || updateMutation.isPending;
  const isLoadingData = mode === 'edit' && isLoadingStudent;

  if (isLoadingData) {
    return (
      <SafeAreaView className='flex-1 bg-white items-center justify-center'>
        <ActivityIndicator size='large' color='#3B82F6' />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView className='flex-1 bg-white'>
      {/* Header */}
      <View className='px-4 py-3 border-b border-gray-200 flex-row items-center justify-between'>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text className='text-blue-500 text-base'>Cancelar</Text>
        </TouchableOpacity>
        <Text className='text-lg font-semibold'>{mode === 'create' ? 'Novo Aluno' : 'Editar Aluno'}</Text>
        <TouchableOpacity onPress={handleSubmit(onSubmit)} disabled={isLoading}>
          {isLoading ? (
            <ActivityIndicator size='small' color='#3B82F6' />
          ) : (
            <Text className='text-blue-500 text-base font-semibold'>Salvar</Text>
          )}
        </TouchableOpacity>
      </View>

      <ScrollView className='flex-1 px-4 py-4'>
        {/* Nome */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Nome *</Text>
          <Controller
            control={control}
            name='name'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value}
                onChangeText={onChange}
                placeholder='Nome completo'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base'
                editable={!isLoading}
              />
            )}
          />
          {errors.name && <Text className='text-red-500 text-xs mt-1'>{errors.name.message}</Text>}
        </View>

        {/* Email */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Email *</Text>
          <Controller
            control={control}
            name='email'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value}
                onChangeText={onChange}
                placeholder='email@exemplo.com'
                keyboardType='email-address'
                autoCapitalize='none'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base'
                editable={!isLoading}
              />
            )}
          />
          {errors.email && <Text className='text-red-500 text-xs mt-1'>{errors.email.message}</Text>}
        </View>

        {/* Telefone */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Telefone</Text>
          <Controller
            control={control}
            name='phone'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value || ''}
                onChangeText={onChange}
                placeholder='(00) 00000-0000'
                keyboardType='phone-pad'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base'
                editable={!isLoading}
              />
            )}
          />
        </View>

        {/* Data de Nascimento */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Data de Nascimento</Text>
          <Controller
            control={control}
            name='birth_date'
            render={({ field: { onChange, value } }) => (
              <>
                <TouchableOpacity
                  onPress={() => setShowDatePicker(true)}
                  className='border border-gray-300 rounded-lg px-3 py-2'
                  disabled={isLoading}
                >
                  <Text className={value ? 'text-gray-900' : 'text-gray-400'}>
                    {value ? value.toLocaleDateString('pt-BR') : 'Selecionar data'}
                  </Text>
                </TouchableOpacity>

                {showDatePicker && (
                  <DateTimePicker
                    value={value || new Date()}
                    mode='date'
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(event, selectedDate) => {
                      setShowDatePicker(Platform.OS === 'ios');
                      if (selectedDate) {
                        onChange(selectedDate);
                      }
                    }}
                    maximumDate={new Date()}
                  />
                )}
              </>
            )}
          />
        </View>

        {/* Gênero */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Gênero</Text>
          <Controller
            control={control}
            name='gender'
            render={({ field: { onChange, value } }) => (
              <View className='border border-gray-300 rounded-lg overflow-hidden'>
                <Picker selectedValue={value || ''} onValueChange={onChange} enabled={!isLoading}>
                  <Picker.Item label='Não informar' value='' />
                  <Picker.Item label='Masculino' value='male' />
                  <Picker.Item label='Feminino' value='female' />
                  <Picker.Item label='Outro' value='other' />
                </Picker>
              </View>
            )}
          />
        </View>

        {/* Altura */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Altura (cm)</Text>
          <Controller
            control={control}
            name='height'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value?.toString() || ''}
                onChangeText={text => {
                  if (text === '') {
                    onChange(null);
                  } else {
                    const num = parseFloat(text);
                    onChange(isNaN(num) ? null : num);
                  }
                }}
                placeholder='170'
                keyboardType='numeric'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base'
                editable={!isLoading}
              />
            )}
          />
        </View>

        {/* Condições Médicas */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Condições Médicas</Text>
          <Controller
            control={control}
            name='medical_conditions'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value || ''}
                onChangeText={onChange}
                placeholder='Descreva condições médicas relevantes...'
                multiline
                numberOfLines={3}
                textAlignVertical='top'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base min-h-[80px]'
                editable={!isLoading}
              />
            )}
          />
        </View>

        {/* Notas */}
        <View className='mb-4'>
          <Text className='text-sm font-medium text-gray-700 mb-1'>Notas</Text>
          <Controller
            control={control}
            name='notes'
            render={({ field: { onChange, value } }) => (
              <TextInput
                value={value || ''}
                onChangeText={onChange}
                placeholder='Observações gerais...'
                multiline
                numberOfLines={3}
                textAlignVertical='top'
                className='border border-gray-300 rounded-lg px-3 py-2 text-base min-h-[80px]'
                editable={!isLoading}
              />
            )}
          />
        </View>

        {/* Status Ativo (apenas edit) */}
        {mode === 'edit' && (
          <View className='mb-4 flex-row items-center justify-between'>
            <Text className='text-sm font-medium text-gray-700'>Aluno Ativo</Text>
            <Controller
              control={control}
              name='is_active'
              render={({ field: { onChange, value } }) => (
                <Switch value={value} onValueChange={onChange} disabled={isLoading} />
              )}
            />
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
};

export default StudentFormScreen;
