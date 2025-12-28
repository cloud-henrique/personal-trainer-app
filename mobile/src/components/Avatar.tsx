import React from 'react';
import { View, Text } from 'react-native';

interface AvatarProps {
  name: string;
  size?: number;
}

const Avatar: React.FC<AvatarProps> = ({ name, size = 40 }) => {
  // Extrai as iniciais do nome (primeira letra do primeiro e último nome)
  const getInitials = (fullName: string): string => {
    const words = fullName
      .trim()
      .split(' ')
      .filter(word => word.length > 0);
    if (words.length === 0) return '?';
    if (words.length === 1) return words[0][0].toUpperCase();
    return (words[0][0] + words[words.length - 1][0]).toUpperCase();
  };

  // Gera uma cor consistente baseada no hash do nome
  const getColorFromName = (fullName: string): string => {
    const colors = [
      'bg-blue-500',
      'bg-green-500',
      'bg-purple-500',
      'bg-pink-500',
      'bg-orange-500',
      'bg-red-500',
      'bg-indigo-500',
      'bg-teal-500',
      'bg-cyan-500',
      'bg-amber-500',
    ];

    // Hash simples do nome
    let hash = 0;
    for (let i = 0; i < fullName.length; i++) {
      hash = fullName.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
  };

  const initials = getInitials(name);
  const bgColor = getColorFromName(name);
  const fontSize = size * 0.4; // Tamanho da fonte é 40% do tamanho do avatar

  return (
    <View className={`${bgColor} items-center justify-center rounded-full`} style={{ width: size, height: size }}>
      <Text className='text-white font-semibold' style={{ fontSize }}>
        {initials}
      </Text>
    </View>
  );
};

export default Avatar;
