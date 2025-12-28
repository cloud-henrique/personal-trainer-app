import React from 'react';
import { View, Text, TouchableOpacity } from 'react-native';

interface EmptyStateProps {
  title: string;
  description?: string;
  icon?: string;
  actionLabel?: string;
  onAction?: () => void;
}

const EmptyState: React.FC<EmptyStateProps> = ({
  title,
  description,
  icon = '📋',
  actionLabel,
  onAction,
}) => {
  return (
    <View className='flex-1 items-center justify-center px-8 py-12'>
      {/* Icon/Emoji */}
      <Text className='text-6xl mb-4'>{icon}</Text>

      {/* Title */}
      <Text className='text-xl font-semibold text-gray-900 text-center mb-2'>
        {title}
      </Text>

      {/* Description */}
      {description && (
        <Text className='text-base text-gray-500 text-center mb-6'>
          {description}
        </Text>
      )}

      {/* Action Button */}
      {actionLabel && onAction && (
        <TouchableOpacity
          onPress={onAction}
          className='bg-blue-500 px-6 py-3 rounded-lg'
          activeOpacity={0.8}
        >
          <Text className='text-white font-semibold text-base'>
            {actionLabel}
          </Text>
        </TouchableOpacity>
      )}
    </View>
  );
};

export default EmptyState;
