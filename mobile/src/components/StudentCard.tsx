import React from 'react';
import { View, Text, TouchableOpacity } from 'react-native';
import Avatar from './Avatar';
import type { Student } from '../types/student';

interface StudentCardProps {
  student: Student;
  onPress: () => void;
}

const StudentCard: React.FC<StudentCardProps> = ({ student, onPress }) => {
  return (
    <TouchableOpacity
      onPress={onPress}
      className='flex-row items-center bg-white p-4 mb-2 rounded-lg border border-gray-200'
      activeOpacity={0.7}
    >
      {/* Avatar */}
      <Avatar name={student.name} size={48} />

      {/* Info */}
      <View className='flex-1 ml-3'>
        <Text className='text-base font-semibold text-gray-900'>{student.name}</Text>
        <Text className='text-sm text-gray-500 mt-0.5'>{student.email}</Text>
      </View>

      {/* Status Badge e Chevron */}
      <View className='items-end'>
        <View className={`px-2 py-1 rounded-full ${student.is_active ? 'bg-green-100' : 'bg-gray-100'}`}>
          <Text className={`text-xs font-medium ${student.is_active ? 'text-green-700' : 'text-gray-500'}`}>
            {student.is_active ? 'Ativo' : 'Inativo'}
          </Text>
        </View>
        <Text className='text-gray-400 mt-1'>›</Text>
      </View>
    </TouchableOpacity>
  );
};

export default StudentCard;
