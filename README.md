# Personal Trainer White-label SaaS

Aplicação SaaS multi-tenant para personal trainers gerenciarem clientes, treinos e progresso.

## 🎯 Status do Projeto

### ✅ Backend (Laravel 12) - COMPLETO

- [x] Multi-tenancy simplificado (BelongsToTenant trait)
- [x] 13 tabelas migradas (tenants, users, students, workouts, etc)
- [x] 10 Models com relacionamentos
- [x] 10 Controllers (Auth, Student, Workout, Exercise, Measurement, Goal, Dashboard)
- [x] Rotas API completas (/api/v1)
- [x] Seeders com 2 tenants de teste
- [x] Laravel Sanctum (autenticação token-based)
- [x] CORS configurado
- [x] Logs por channel (students, workouts, auth)
- [x] Validação backend (Request classes)
- [x] API Resources (transformers de response)

### ✅ Mobile (React Native + Expo) - FUNDAÇÃO COMPLETA

- [x] Estrutura Expo SDK 52 + TypeScript
- [x] NativeWind 4 (Tailwind CSS)
- [x] Zustand stores com AsyncStorage (auth, theme)
- [x] React Navigation 6 (Auth/App stacks)
- [x] TanStack Query v5 (React Query)
- [x] Axios com interceptors
- [x] Telas de Login e Registro
- [x] Dashboard básico
- [x] Tema dinâmico por tenant

### 🚧 Próximos Passos

- [ ] CRUD de Alunos (mobile)
- [ ] CRUD de Treinos (mobile)
- [ ] Medições e Gráficos (mobile)
- [ ] Push Notifications
- [ ] Offline-first (fila de mutações)

## 📁 Estrutura do Projeto

```
personal-trainer-app/
├── api/                   # Laravel 12 Backend
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Models/Traits/BelongsToTenant.php  ⭐ Core multi-tenancy
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   └── .env
│
├── mobile/                # React Native + Expo
│   ├── src/
│   │   ├── screens/
│   │   │   ├── auth/      (Login, Register)
│   │   │   ├── trainer/   (Dashboard)
│   │   │   └── student/
│   │   ├── stores/        (authStore, themeStore)
│   │   ├── services/      (api, authService)
│   │   └── navigation/
│   ├── App.tsx
│   └── package.json
│
└── CLAUDE.md             # Documentação completa do projeto
```

## 🚀 Início Rápido

### Backend

```bash
cd api

# Instalar dependências
composer install

# Configurar .env
cp .env.example .env
# Editar .env com dados do MySQL

# Rodar migrations + seeders
php artisan migrate:fresh --seed

# Iniciar servidor
php artisan serve
```

**API rodando em:** `http://localhost:8000/api/v1`

### Mobile

```bash
cd mobile

# Instalar dependências
npm install

# Iniciar Expo
npm start

# Ou rodar direto
npm run ios    # iOS
npm run android  # Android
```

## 🔑 Credenciais de Teste

### Tenant 1 - Academia Fitness Pro

- **Email:** carlos@fitnesspro.com
- **Senha:** password123
- **Cor primária:** #FF6B6B

### Tenant 2 - Personal Strong

- **Email:** ana@personalstrong.com
- **Senha:** password123
- **Cor primária:** #4ECDC4

## 🏗️ Arquitetura Multi-Tenant

### Backend - Single Database com tenant_id

```php
// Trait BelongsToTenant (auto-filtra queries)
trait BelongsToTenant {
    protected static function bootBelongsToTenant(): void {
        static::creating(fn($model) => $model->tenant_id = auth()->user()->tenant_id);
        static::addGlobalScope('tenant', fn($builder) =>
            $builder->where('tenant_id', auth()->user()->tenant_id)
        );
    }
}

// Todos os models (exceto Tenant) usam o trait
class Student extends Model {
    use BelongsToTenant;
}
```

### Mobile - Tema Dinâmico

```typescript
// Carregado após login
const { theme } = useThemeStore();

// Usar em componentes
<View style={{ backgroundColor: theme.primaryColor }} />
<Text style={{ color: theme.primaryColor }}>...</Text>
```

## 📡 API Endpoints

### Autenticação (Públicas)

```
POST /api/v1/auth/register   # Criar tenant + personal
POST /api/v1/auth/login      # Login
```

### Protegidas (auth:sanctum)

```
POST   /api/v1/auth/logout
GET    /api/v1/auth/me

GET    /api/v1/dashboard/stats
GET    /api/v1/dashboard/recent-activity

GET    /api/v1/students
POST   /api/v1/students
GET    /api/v1/students/{id}
PUT    /api/v1/students/{id}
DELETE /api/v1/students/{id}

GET    /api/v1/students/{id}/measurements
POST   /api/v1/students/{id}/measurements
GET    /api/v1/students/{id}/measurements/latest
GET    /api/v1/students/{id}/measurements/graph

GET    /api/v1/workouts
POST   /api/v1/workouts
GET    /api/v1/workouts/{id}
PUT    /api/v1/workouts/{id}
DELETE /api/v1/workouts/{id}

POST   /api/v1/workouts/{id}/exercises
PUT    /api/v1/exercises/{id}
DELETE /api/v1/exercises/{id}

GET    /api/v1/students/{id}/goals
POST   /api/v1/students/{id}/goals
PUT    /api/v1/goals/{id}
DELETE /api/v1/goals/{id}
```

## 🗄️ Banco de Dados

### Tabelas Principais

- `tenants` - Personal trainers (UUID primary key)
- `users` - Admins/Trainers do tenant
- `students` - Alunos do personal
- `student_measurements` - Avaliações físicas
- `workouts` - Fichas de treino
- `exercises` - Exercícios das fichas
- `workout_logs` - Registros de execução
- `goals` - Metas dos alunos
- `payments` - Mensalidades (futuro)

**Total:** 13 tabelas migradas

## 🎨 Stack Tecnológica

### Backend

- Laravel 12
- Laravel Sanctum (auth)
- MySQL/MariaDB
- XAMPP (dev)
- PHP 8.3

### Mobile

- React Native
- Expo SDK 52
- TypeScript (strict)
- NativeWind 4 (Tailwind)
- Zustand (state)
- TanStack Query v5
- React Navigation 6
- Axios

## 📱 Features Implementadas

### Backend

✅ Sistema multi-tenant com isolamento por tenant_id
✅ Autenticação JWT com Sanctum
✅ CRUD completo de Students, Workouts, Exercises, Measurements, Goals
✅ Sistema de medições e gráficos
✅ Sistema de metas
✅ Dashboard com estatísticas
✅ Logs estruturados por entidade
✅ Validação com Form Requests
✅ Respostas padronizadas com API Resources

### Mobile

✅ Login/Registro com tema dinâmico
✅ Persistência de autenticação
✅ Navegação Auth/App
✅ Stores com AsyncStorage
✅ API client configurado
✅ Tema customizado por tenant

## 🔜 Próximos Passos

1. **Mobile - Lista de Alunos**

   - StudentListScreen
   - StudentService com React Query
   - Card component

2. **Mobile - Detalhes do Aluno**

   - StudentDetailScreen
   - Tabs (Info, Medições, Treinos, Metas)

3. **Mobile - Criar Treino**

   - WorkoutFormScreen
   - ExerciseList component

4. **Backend - Validações**

   - Form Requests estruturados
   - API Resources para responses

5. **Deploy**
   - API na VPS Hostinger
   - Mobile via EAS Build

## 📚 Documentação

Ver [CLAUDE.md](./CLAUDE.md) para:

- Arquitetura detalhada
- Regras de negócio
- Breaking changes do Laravel 12
- Fluxos de autenticação
- Convenções de código

## 🧪 Testes

```bash
# Backend - Testar login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos@fitnesspro.com","password":"password123"}'

# Mobile - Rodar app
npm start
```

## 👨‍💻 Desenvolvimento

### Comandos Úteis

```bash
# Backend
php artisan migrate:fresh --seed  # Reset DB
php artisan tinker                # REPL
php artisan route:list            # Ver rotas

# Mobile
npm run ios                       # iOS simulator
npm run android                   # Android emulator
npm start                         # Metro bundler
```

## 📄 Licença

Projeto privado - Pulsatrix

---

**Última atualização:** 28/12/2024
**Status:** Backend completo (validações + resources) + Mobile foundation completa
