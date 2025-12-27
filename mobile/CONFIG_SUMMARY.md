# Configuração Mobile - Personal Trainer App

## ✅ Configuração Atual (NativeWind v2)

### Versões Instaladas
- **NativeWind:** 2.0.11 (estável)
- **Tailwind CSS:** 3.3.2
- **Expo:** ~54.0.30
- **React Native:** 0.81.5
- **TypeScript:** ~5.9.2

### Arquivos de Configuração

**babel.config.js:**
```javascript
module.exports = function (api) {
  api.cache(true);
  return {
    presets: ['babel-preset-expo'],
    plugins: ['nativewind/babel'],
  };
};
```

**metro.config.js:**
```javascript
const { getDefaultConfig } = require('expo/metro-config');

module.exports = getDefaultConfig(__dirname);
```

**tailwind.config.js:**
```javascript
module.exports = {
  content: [
    "./App.{js,jsx,ts,tsx}",
    "./src/**/*.{js,jsx,ts,tsx}"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

**tsconfig.json:**
```json
{
  "extends": "expo/tsconfig.base",
  "compilerOptions": {
    "strict": true,
    "paths": {
      "@/*": ["./src/*"]
    }
  },
  "include": [
    "**/*.ts",
    "**/*.tsx"
  ]
}
```

## 🚀 Como Rodar

```bash
# Limpar cache e iniciar
npx expo start -c

# Pressionar 'i' para iOS
# Pressionar 'a' para Android
```

## 🎨 Usando NativeWind

### Classe Estática (NativeWind)
```tsx
<View className="flex-1 bg-white p-4">
  <Text className="text-xl font-bold text-gray-800">
    Hello World
  </Text>
</View>
```

### Estilo Dinâmico (Inline Style)
```tsx
const { theme } = useThemeStore();

<View style={{ backgroundColor: theme.primaryColor }}>
  <Text style={{ color: theme.primaryColor }}>
    Themed Text
  </Text>
</View>
```

### Combinando NativeWind + Inline
```tsx
<TouchableOpacity
  className="rounded-lg py-4 px-6"
  style={{ backgroundColor: theme.primaryColor }}
>
  <Text className="text-white font-bold text-center">
    Button
  </Text>
</TouchableOpacity>
```

## ⚠️ Regras Importantes

1. **NÃO use interpolação em className:**
   ```tsx
   // ❌ ERRADO
   className={`bg-[${color}]`}

   // ✅ CORRETO
   style={{ backgroundColor: color }}
   ```

2. **Valores dinâmicos = inline style:**
   ```tsx
   // Use style para cores dinâmicas do tenant
   style={{ backgroundColor: theme.primaryColor }}
   ```

3. **Classes estáticas = className:**
   ```tsx
   // Use className para estilos fixos
   className="flex-1 bg-white p-4 rounded-lg"
   ```

## 🔧 Troubleshooting

### Erro: "expected dynamic type 'boolean'"
**Solução:** Certifique-se de estar usando NativeWind v2 (2.0.11)

```bash
npm uninstall nativewind tailwindcss
npm install nativewind@^2.0.11 tailwindcss@3.3.2
npx expo start -c
```

### Styles não aplicam
**Solução:** Limpe o cache

```bash
rm -rf node_modules/.cache
rm -rf .expo
npx expo start -c
```

### TypeScript errors
**Solução:** Verifique tsconfig.json (sem referências a nativewind-env.d.ts)

## 📱 Estrutura das Telas

Todas as telas seguem este padrão:

```tsx
import { View, Text } from 'react-native';
import { useThemeStore } from '../../stores/themeStore';

export default function MyScreen() {
  const { theme } = useThemeStore();

  return (
    <View className="flex-1 bg-white p-4">
      {/* Header com cor do tenant */}
      <Text
        className="text-2xl font-bold mb-4"
        style={{ color: theme.primaryColor }}
      >
        Título
      </Text>

      {/* Content com classes NativeWind */}
      <View className="bg-gray-100 rounded-lg p-4">
        <Text className="text-gray-800">Content</Text>
      </View>
    </View>
  );
}
```

## 🎯 Próximas Implementações

1. **StudentListScreen** - Lista de alunos com FlatList
2. **StudentDetailScreen** - Detalhes do aluno com Tabs
3. **WorkoutFormScreen** - Criar/editar treino
4. **ExerciseCard** - Component reutilizável

## 📚 Referências

- [NativeWind v2 Docs](https://www.nativewind.dev/v2/overview)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Expo Docs](https://docs.expo.dev/)
