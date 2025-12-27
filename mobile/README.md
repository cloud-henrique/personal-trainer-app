# Personal Trainer App - Mobile

Aplicação mobile em React Native + Expo para o Personal Trainer SaaS.

## Tecnologias

- **React Native** com **Expo SDK 54**
- **TypeScript** (strict mode)
- **NativeWind 2.0.11** (Tailwind CSS para React Native - versão estável)
- **Zustand** (gerenciamento de estado com persistência)
- **TanStack Query v5** (React Query para cache e sincronização)
- **Axios** (cliente HTTP)
- **React Navigation 6** (navegação)
- **React Hook Form + Zod** (formulários e validação)

## Estrutura de Diretórios

```
src/
├── components/         # Componentes reutilizáveis
├── screens/
│   ├── auth/          # Telas de autenticação (Login, Register)
│   ├── trainer/       # Telas do personal trainer
│   └── student/       # Telas do aluno
├── services/          # API e services (axios, react-query)
├── stores/            # Zustand stores (auth, theme)
├── hooks/             # Custom hooks
├── navigation/        # React Navigation setup
├── utils/             # Utilitários
└── types/             # TypeScript types
```

## Instalação

```bash
# Instalar dependências
npm install

# Iniciar desenvolvimento
npm start

# Rodar no iOS
npm run ios

# Rodar no Android
npm run android
```

## Tema Dinâmico

O app suporta temas dinâmicos por tenant. Quando o usuário faz login, o tema do tenant (cor primária, logo, cover) é carregado e persiste no AsyncStorage.

```typescript
import { useThemeStore } from './stores/themeStore';

const { theme } = useThemeStore();
// theme.primaryColor, theme.logoUrl, theme.coverUrl, theme.name
```

## Autenticação

Sistema de autenticação com Sanctum tokens:

```typescript
import { useLogin, useRegister, useLogout } from './services/authService';

// Login
const loginMutation = useLogin();
await loginMutation.mutateAsync({ email, password });

// Register
const registerMutation = useRegister();
await registerMutation.mutateAsync({ name, email, slug, password, password_confirmation });

// Logout
const logoutMutation = useLogout();
await logoutMutation.mutateAsync();
```

## API Base URL

Alterar em `src/services/api.ts`:

```typescript
const API_BASE_URL = 'http://localhost:8000/api/v1'; // Dev
// const API_BASE_URL = 'https://api.seudominio.com/api/v1'; // Prod
```

## Credenciais de Teste

```
Tenant 1: carlos@fitnesspro.com / password123
Tenant 2: ana@personalstrong.com / password123
```

## Build e Deploy

```bash
# Configurar EAS
npm install -g eas-cli
eas login

# Build para produção
eas build --platform all

# Publicar atualização OTA
eas update --branch production
```

## Variáveis de Ambiente

Criar arquivo `.env`:

```ini
API_BASE_URL=http://localhost:8000/api/v1
```

## Offline-first

O app usa TanStack Query com cache persistente para funcionar offline. Mutações são enfileiradas automaticamente quando offline.

## Notas

- **Modo Dev**: Credenciais de teste aparecem na tela de login
- **Tema**: Sempre usar `theme.primaryColor` em componentes customizados
- **TypeScript**: Strict mode habilitado
- **Tailwind**: Usar className com NativeWind
