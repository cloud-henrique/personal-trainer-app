# Troubleshooting - Personal Trainer Mobile App

## Erro: "TypeError: expected dynamic type 'boolean', but had type 'string'"

Este erro ocorre devido a incompatibilidades do NativeWind 4 com Expo. Siga os passos abaixo:

### Solução 1: Limpar Cache e Reiniciar

```bash
# 1. Parar o servidor Metro (Ctrl+C)

# 2. Limpar cache do npm e Metro
rm -rf node_modules
npm install
npx expo start -c

# 3. Reiniciar o simulador iOS
# Pressione 'i' no terminal do Expo
```

### Solução 2: Verificar Configurações

**babel.config.js** deve estar assim:

```javascript
module.exports = function (api) {
  api.cache(true);
  return {
    presets: ['babel-preset-expo'],
    plugins: ['nativewind/babel'],
  };
};
```

**metro.config.js** deve estar assim:

```javascript
const { getDefaultConfig } = require('expo/metro-config');

const config = getDefaultConfig(__dirname);

const { withNativeWind } = require('nativewind/metro');

module.exports = withNativeWind(config, { input: './global.css' });
```

**tsconfig.json** deve incluir:

```json
{
  "include": ["**/*.ts", "**/*.tsx", "nativewind-env.d.ts", "app.d.ts"]
}
```

### Solução 3: Versões Corretas

Certifique-se de ter estas versões no `package.json`:

```json
{
  "dependencies": {
    "nativewind": "^4.0.1",
    "tailwindcss": "3.3.2"
  }
}
```

### Solução 4: Se o Erro Persistir

Use `style` ao invés de `className` para valores dinâmicos:

**❌ Incorreto:**

```tsx
<View className={`bg-[${theme.primaryColor}]`}>
```

**✅ Correto:**

```tsx
<View style={{ backgroundColor: theme.primaryColor }}>
```

### Comandos Úteis

```bash
# Limpar cache completo
npx expo start -c

# Resetar Metro bundler
rm -rf .expo
rm -rf node_modules/.cache

# Reinstalar tudo
rm -rf node_modules
npm install

# Verificar versões
npm list nativewind tailwindcss
```

## Outros Erros Comuns

### "Cannot find module 'nativewind/preset'"

Reinstale o NativeWind:

```bash
npm uninstall nativewind
npm install nativewind@^4.0.1
```

### "Module not found: Can't resolve './global.css'"

Certifique-se de que `global.css` existe na raiz do projeto mobile:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### Problema com AsyncStorage

```bash
npx expo install @react-native-async-storage/async-storage
```

### Build iOS falha

```bash
# Limpar build do iOS
cd ios
rm -rf Pods Podfile.lock
pod install --repo-update
cd ..
```

## Testando a API

```bash
# Em outro terminal, certifique-se de que o Laravel está rodando
cd ../api
php artisan serve

# Teste o endpoint de login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos@fitnesspro.com","password":"password123"}'
```

## Links Úteis

- [NativeWind v4 Docs](https://www.nativewind.dev/v4/overview)
- [Expo Troubleshooting](https://docs.expo.dev/troubleshooting/clear-cache-windows/)
- [React Navigation Docs](https://reactnavigation.org/docs/getting-started)
