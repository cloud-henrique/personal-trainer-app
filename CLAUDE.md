# CONTEXTO DO PROJETO - Personal Trainer White-label App

## VISÃO GERAL

Você está construindo uma **aplicação SaaS white-label multi-tenant** para personal trainers gerenciarem clientes, treinos e acompanharem o progresso. O sistema usa **isolamento por banco de dados** (database-per-tenant) para segurança e escalabilidade.

Este é um **MVP** focado em funcionalidades essenciais, sem over-engineering, mas com arquitetura multi-tenant robusta e funcional.

---

## STACK TECNOLÓGICA

### Backend (API)
- **Laravel 12** (versão mais recente - ATENÇÃO às breaking changes da v11→v12)
- **Multi-tenancy**: Pacote `stancl/tenancy` (arquitetura database-per-tenant)
- **Autenticação**: Laravel Sanctum (token-based para mobile)
- **Banco de Dados**: MySQL/MariaDB (XAMPP local para dev, VPS Hostinger para produção)
- **Servidor**: VPS Hostinger (configuração production-ready)
- **Logs**: Laravel Log com channels por model, retenção de 7 dias

### Frontend (Mobile)
- **React Native** com **Expo SDK 52**
- **Expo EAS Update** (atualizações OTA para iteração rápida)
- **UI**: NativeWind 4.x (Tailwind CSS para React Native)
- **Gerenciamento de Estado**: Zustand com persistência AsyncStorage
- **Formulários**: React Hook Form + Zod para validação
- **Cliente API**: Axios + TanStack Query v5 (React Query)
- **Navegação**: React Navigation 6
- **Offline-first**: TanStack Query com cache persistente + fila de mutações

### Código Compartilhado
- **TypeScript** types compartilhados entre API e mobile (pasta `/shared`)
- Schemas de validação Zod compartilhados para consistência
- Constantes de rotas da API

### Ambiente de Desenvolvimento
- **OS**: macOS (Apple Silicon M3)
- **Servidor Local**: XAMPP (Apache + MariaDB + PHP)
- **Gerenciador de Banco**: phpMyAdmin
- **IDE**: Cursor com extensão Claude Code

---

## ARQUITETURA DO BANCO DE DADOS

### Banco Central (`personal_trainer`)
Gerencia os tenants e configurações globais:

```sql
-- Tabela de tenants (personal trainers)
tenants (
  id UUID PRIMARY KEY,
  name VARCHAR,
  slug VARCHAR UNIQUE,
  primary_color VARCHAR DEFAULT '#3B82F6',
  logo_url VARCHAR NULLABLE,
  cover_url VARCHAR NULLABLE,
  email VARCHAR UNIQUE,
  phone VARCHAR NULLABLE,
  plan ENUM('free', 'basic', 'premium') DEFAULT 'free',
  is_active BOOLEAN DEFAULT true,
  trial_ends_at TIMESTAMP NULLABLE,
  data JSON NULLABLE,
  timestamps,
  soft_deletes
)

-- Domínios/subdomínios de cada tenant
domains (
  id INTEGER PRIMARY KEY,
  tenant_id UUID FOREIGN KEY → tenants.id,
  domain VARCHAR UNIQUE,
  is_primary BOOLEAN DEFAULT false,
  timestamps
)
```

### Bancos dos Tenants (`tenant_{uuid}`)
Cada personal trainer tem banco isolado com:

```sql
-- Usuários do tenant (trainers/admins)
users (
  id BIGINT PRIMARY KEY,
  name VARCHAR,
  email VARCHAR UNIQUE,
  password VARCHAR,
  phone VARCHAR NULLABLE,
  avatar_url VARCHAR NULLABLE,
  role ENUM('admin', 'trainer', 'student') DEFAULT 'trainer',
  is_active BOOLEAN DEFAULT true,
  email_verified_at TIMESTAMP NULLABLE,
  remember_token VARCHAR,
  timestamps,
  soft_deletes
)

-- Alunos do personal
students (
  id BIGINT PRIMARY KEY,
  name VARCHAR,
  email VARCHAR UNIQUE,
  phone VARCHAR NULLABLE,
  birth_date DATE NULLABLE,
  avatar_url VARCHAR NULLABLE,
  gender ENUM('male', 'female', 'other') NULLABLE,
  height DECIMAL(5,2) NULLABLE,
  medical_conditions TEXT NULLABLE,
  notes TEXT NULLABLE,
  trainer_id BIGINT FOREIGN KEY → users.id NULL ON DELETE,
  is_active BOOLEAN DEFAULT true,
  timestamps,
  soft_deletes
)

-- Medições/avaliações físicas
student_measurements (
  id BIGINT PRIMARY KEY,
  student_id BIGINT FOREIGN KEY → students.id CASCADE,
  weight DECIMAL(5,2),
  body_fat DECIMAL(5,2) NULLABLE,
  muscle_mass DECIMAL(5,2) NULLABLE,
  chest DECIMAL(5,2) NULLABLE,
  waist DECIMAL(5,2) NULLABLE,
  hips DECIMAL(5,2) NULLABLE,
  right_arm DECIMAL(5,2) NULLABLE,
  left_arm DECIMAL(5,2) NULLABLE,
  right_thigh DECIMAL(5,2) NULLABLE,
  left_thigh DECIMAL(5,2) NULLABLE,
  right_calf DECIMAL(5,2) NULLABLE,
  left_calf DECIMAL(5,2) NULLABLE,
  notes TEXT NULLABLE,
  measured_at DATE,
  timestamps,
  INDEX (student_id, measured_at)
)

-- Fichas de treino
workouts (
  id BIGINT PRIMARY KEY,
  student_id BIGINT FOREIGN KEY → students.id CASCADE,
  created_by BIGINT FOREIGN KEY → users.id,
  name VARCHAR,
  description TEXT NULLABLE,
  category ENUM('strength', 'cardio', 'flexibility', 'mixed') DEFAULT 'strength',
  starts_at DATE NULLABLE,
  ends_at DATE NULLABLE,
  is_active BOOLEAN DEFAULT true,
  timestamps,
  soft_deletes
)

-- Exercícios da ficha
exercises (
  id BIGINT PRIMARY KEY,
  workout_id BIGINT FOREIGN KEY → workouts.id CASCADE,
  order INTEGER DEFAULT 0,
  name VARCHAR,
  muscle_group VARCHAR NULLABLE,
  description TEXT NULLABLE,
  video_url VARCHAR NULLABLE,
  sets INTEGER DEFAULT 3,
  reps VARCHAR,
  rest VARCHAR DEFAULT '60s',
  load VARCHAR NULLABLE,
  tempo VARCHAR NULLABLE,
  notes TEXT NULLABLE,
  timestamps
)

-- Registros de execução dos treinos
workout_logs (
  id BIGINT PRIMARY KEY,
  workout_id BIGINT FOREIGN KEY → workouts.id CASCADE,
  exercise_id BIGINT FOREIGN KEY → exercises.id CASCADE,
  student_id BIGINT FOREIGN KEY → students.id CASCADE,
  performed_at DATE,
  set_number INTEGER,
  reps_completed INTEGER,
  load_used DECIMAL(8,2) NULLABLE,
  notes TEXT NULLABLE,
  timestamps,
  INDEX (student_id, performed_at)
)

-- Metas dos alunos
goals (
  id BIGINT PRIMARY KEY,
  student_id BIGINT FOREIGN KEY → students.id CASCADE,
  title VARCHAR,
  description TEXT NULLABLE,
  type ENUM('weight_loss', 'muscle_gain', 'performance', 'other'),
  target_value DECIMAL(8,2) NULLABLE,
  current_value DECIMAL(8,2) NULLABLE,
  unit VARCHAR NULLABLE,
  starts_at DATE,
  target_date DATE NULLABLE,
  completed_at DATE NULLABLE,
  status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
  timestamps
)

-- Pagamentos/mensalidades (futura implementação com Asaas)
payments (
  id BIGINT PRIMARY KEY,
  student_id BIGINT FOREIGN KEY → students.id CASCADE,
  amount DECIMAL(10,2),
  due_date DATE,
  paid_at DATE NULLABLE,
  status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
  payment_method ENUM('cash', 'pix', 'credit_card', 'debit_card', 'bank_transfer') NULLABLE,
  notes TEXT NULLABLE,
  timestamps,
  INDEX (student_id, due_date),
  INDEX (status)
)
```

---

## ESTRUTURA DO PROJETO

```
personal-trainer-app/
├── api/                              # Laravel 12 Backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   └── LogoutController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── WorkoutController.php
│   │   │   │   ├── ExerciseController.php
│   │   │   │   ├── MeasurementController.php
│   │   │   │   ├── WorkoutLogController.php
│   │   │   │   ├── GoalController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Middleware/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   │   ├── Tenant.php
│   │   │   ├── User.php
│   │   │   ├── Student.php
│   │   │   ├── Workout.php
│   │   │   ├── Exercise.php
│   │   │   ├── StudentMeasurement.php
│   │   │   ├── WorkoutLog.php
│   │   │   ├── Goal.php
│   │   │   └── Payment.php
│   │   ├── Services/
│   │   │   ├── TenantService.php
│   │   │   └── NotificationService.php
│   │   └── Providers/
│   ├── bootstrap/
│   │   └── app.php                  # Laravel 12: substitui Kernel
│   ├── config/
│   │   ├── tenancy.php
│   │   ├── sanctum.php
│   │   ├── cors.php
│   │   └── logging.php
│   ├── database/
│   │   ├── migrations/              # Central DB
│   │   ├── migrations/tenant/       # Tenant DBs
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   └── storage/logs/
│
├── mobile/                           # React Native + Expo
│   ├── src/
│   │   ├── components/
│   │   ├── screens/
│   │   │   ├── auth/
│   │   │   ├── trainer/
│   │   │   └── student/
│   │   ├── services/
│   │   ├── stores/
│   │   ├── hooks/
│   │   ├── navigation/
│   │   └── utils/
│   ├── app.json
│   ├── eas.json
│   └── package.json
│
└── shared/                           # TypeScript compartilhado
    ├── types/
    ├── constants/
    └── validations/
```

---

## BREAKING CHANGES DO LARAVEL 12

### ⚠️ CRÍTICO: Mudanças obrigatórias

#### 1. Bootstrap (`bootstrap/app.php`)
Laravel 12 removeu `app/Http/Kernel.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        $middleware->alias([
            'tenant' => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
        ]);
        
        $middleware->validateCsrfTokens(except: ['api/*']);
    })
    ->create();
```

#### 2. CORS (`config/cors.php`)
```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS')),
    'supports_credentials' => true,
];
```

#### 3. .env
```ini
DB_SOCKET=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock
CORS_ALLOWED_ORIGINS=http://localhost:8081
TENANCY_DATABASE_PREFIX=tenant
```

---

## REGRAS DE NEGÓCIO

### Autenticação e Permissões
1. **Personal Trainers**: 
   - Se cadastram com auto-registro (plano free)
   - Criam novo tenant automaticamente
   - Têm role `admin` em seu tenant
   
2. **Alunos**:
   - São cadastrados pelo personal (não podem se auto-registrar)
   - Recebem email com credenciais de acesso
   - Têm role `student` no tenant
   - Só acessam seus próprios dados e treinos

3. **Super Admin**:
   - Apenas devs (gerenciam tenants no banco central)
   - Não implementar interface para isso no MVP

### Planos
- **free**: Padrão no registro
- **basic/premium**: Futura implementação com Asaas (gateway de pagamento)

### Notificações Push
- Enviar quando personal criar novo treino para aluno
- Usar Expo Push Notifications

### Offline-first
- App mobile deve funcionar offline
- Sincronizar mutações quando voltar online
- TanStack Query com cache persistente

---

## ROTAS DA API

### Autenticação
```
POST   /api/v1/auth/register          # Criar tenant + personal
POST   /api/v1/auth/login             # Login
POST   /api/v1/auth/logout            # Logout
GET    /api/v1/auth/me                # Usuário atual
```

### Alunos (`auth:sanctum`)
```
GET    /api/v1/students               # Listar
POST   /api/v1/students               # Criar (envia email)
GET    /api/v1/students/{id}          # Detalhes
PUT    /api/v1/students/{id}          # Atualizar
DELETE /api/v1/students/{id}          # Deletar
```

### Medições (`auth:sanctum`)
```
GET    /api/v1/students/{id}/measurements
POST   /api/v1/students/{id}/measurements
GET    /api/v1/students/{id}/measurements/latest
GET    /api/v1/students/{id}/measurements/graph
```

### Treinos (`auth:sanctum`)
```
GET    /api/v1/students/{id}/workouts
POST   /api/v1/students/{id}/workouts
GET    /api/v1/workouts/{id}
PUT    /api/v1/workouts/{id}
DELETE /api/v1/workouts/{id}
```

### Exercícios (`auth:sanctum`)
```
POST   /api/v1/workouts/{id}/exercises
PUT    /api/v1/exercises/{id}
DELETE /api/v1/exercises/{id}
```

### Logs de Treino (`auth:sanctum`)
```
GET    /api/v1/students/{id}/workout-logs
POST   /api/v1/workout-logs
```

### Metas (`auth:sanctum`)
```
GET    /api/v1/students/{id}/goals
POST   /api/v1/students/{id}/goals
PUT    /api/v1/goals/{id}
DELETE /api/v1/goals/{id}
```

### Dashboard (`auth:sanctum`)
```
GET    /api/v1/dashboard/stats
GET    /api/v1/dashboard/recent-activity
```

---

## FORMATO DE RESPOSTA

### Sucesso
```json
{
  "success": true,
  "data": {...},
  "message": "Mensagem de sucesso"
}
```

### Erro de Validação
```json
{
  "success": false,
  "message": "Erro de validação",
  "errors": {
    "email": ["O campo email é obrigatório"]
  }
}
```

### Erro Geral
```json
{
  "success": false,
  "message": "Mensagem de erro"
}
```

---

## FLUXO DE REGISTRO (TENANT)

1. Personal preenche formulário de registro
2. Backend valida dados
3. Cria registro na tabela `tenants` (banco central)
4. Cria domínio: `{slug}.localhost` (dev) ou `{slug}.app.com` (prod)
5. Cria banco `tenant_{uuid}` 
6. Roda migrations no banco do tenant
7. Cria usuário admin no banco do tenant
8. Retorna token + user + tenant

---

## FLUXO DE LOGIN

1. Usuário envia email + senha
2. Backend busca tenant pelo email
3. Inicializa contexto do tenant (`tenancy()->initialize()`)
4. Valida credenciais no banco do tenant
5. Cria token Sanctum
6. Retorna token + user + tenant (dados para tema dinâmico)

---

## TEMA DINÂMICO (MOBILE)

Cada tenant tem:
- `primary_color` (cor primária do app)
- `logo_url` (logo exibido no login/header)
- `cover_url` (imagem de capa do login)

```typescript
// stores/themeStore.ts
interface TenantTheme {
  primaryColor: string
  logoUrl: string | null
  coverUrl: string | null
  name: string
}

// Armazenar após login
setTheme(response.data.tenant)

// Usar em componentes
const { theme } = useThemeStore()
<View style={{ backgroundColor: theme.primaryColor }} />
```

---

## OFFLINE-FIRST (MOBILE)

### Cache Persistente (React Query)
```typescript
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      cacheTime: 1000 * 60 * 60 * 24, // 24h
      staleTime: 1000 * 60 * 5, // 5min
    },
  },
})

const persister = createAsyncStoragePersister({
  storage: AsyncStorage,
})
```

### Fila de Mutações
```typescript
// stores/offlineStore.ts
interface PendingMutation {
  id: string
  endpoint: string
  method: 'POST' | 'PUT' | 'DELETE'
  data: any
}

// Quando offline, adicionar à fila
addPendingMutation({ endpoint: '/students', method: 'POST', data })

// Quando voltar online, sincronizar
syncPendingMutations()
```

---

## PUSH NOTIFICATIONS

### Setup (Mobile)
```typescript
import * as Notifications from 'expo-notifications'

// Solicitar permissão
const token = await registerForPushNotifications()

// Enviar token para backend
api.post('/users/push-token', { token })
```

### Envio (Backend)
```php
// app/Services/NotificationService.php
public function sendWorkoutNotification($student, $workout)
{
    Http::post('https://exp.host/--/api/v2/push/send', [
        'to' => $student->push_token,
        'title' => 'Novo Treino!',
        'body' => "Você tem um novo treino: {$workout->name}",
    ]);
}
```

---

## DEPLOY VPS HOSTINGER

### Estrutura
```
/var/www/personal-trainer/
├── api/
│   ├── public/          # Document root
│   └── storage/
└── .env (production)
```

### Nginx Config
```nginx
server {
    listen 80;
    server_name api.seudominio.com;
    root /var/www/personal-trainer/api/public;
    
    location / {
        try_files $uri /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        include fastcgi_params;
    }
}
```

### SSL
```bash
certbot --nginx -d api.seudominio.com
```

### Permissões
```bash
chown -R www-data:www-data /var/www/personal-trainer
chmod -R 755 /var/www/personal-trainer
chmod -R 775 /var/www/personal-trainer/api/storage
```

---

## LOGS

### Configuração (`config/logging.php`)
```php
'channels' => [
    'students' => [
        'driver' => 'daily',
        'path' => storage_path('logs/students.log'),
        'days' => 7,
    ],
    'workouts' => [
        'driver' => 'daily',
        'path' => storage_path('logs/workouts.log'),
        'days' => 7,
    ],
],
```

### Uso
```php
Log::channel('students')->info('Student created', [
    'student_id' => $student->id,
    'tenant_id' => tenant('id'),
]);
```

---

## PADRÕES DE CÓDIGO

### PHP
- PSR-12
- Type hints obrigatórios
- Form Requests para validação
- API Resources para transformação
- snake_case (variáveis, BD)

### TypeScript
- Functional components
- Strict mode
- Custom hooks
- camelCase (variáveis)
- PascalCase (componentes)

### Commits
```
feat: adiciona endpoint de criação de treino
fix: corrige erro ao deletar aluno
docs: atualiza README
```

---

## PRIORIDADES DO MVP

### Backend (Alta Prioridade)
1. ✅ Multi-tenancy configurado (BelongsToTenant trait)
2. ✅ Migrations completas (13 tabelas)
3. ✅ Models com relacionamentos (10 models)
4. ✅ Seeders com dados de teste (2 tenants completos)
5. ✅ Controllers (Auth, Student, Workout, Exercise, Measurement, Goal, Dashboard)
6. ✅ Rotas definidas (routes/api.php completo)
7. ✅ TenantService criado
8. [ ] Form Requests (validações estruturadas)
9. [ ] API Resources (transformers de response)

### Mobile (Alta Prioridade)
1. ✅ Estrutura base (Expo + NativeWind 4)
2. ✅ Tema dinâmico (Zustand com persistência)
3. ✅ Auth flow (Login/Register com React Query)
4. ✅ Navegação (React Navigation 6)
5. ✅ Stores (authStore, themeStore com AsyncStorage)
6. ✅ API Service (Axios com interceptors)
7. [ ] Lista de alunos
8. [ ] Detalhes do aluno
9. [ ] Criar treino
10. [ ] Visualizar treino (aluno)

### Futuro (Baixa Prioridade)
- Sistema de pagamento (Asaas)
- Gráficos avançados
- Chat trainer-aluno
- Calendário de treinos
- Relatórios PDF

---

## COMANDOS ÚTEIS

### Backend (Dev)
```bash
cd api
php artisan serve
php artisan migrate:fresh --seed
php artisan tinker
```

### Mobile (Dev)
```bash
cd mobile
npm start
npx expo start --clear
```

### Deploy (EAS)
```bash
eas update --branch production
eas build --platform all
```

---

## OBSERVAÇÕES IMPORTANTES

1. **Multi-tenancy é CRÍTICO**: Todo acesso ao banco do tenant DEVE estar dentro do contexto (`tenancy()->initialize()` ou `$tenant->run()`)

2. **Offline-first**: Todas as mutações devem ir para fila se offline

3. **Tema dinâmico**: SEMPRE usar `theme.primaryColor` nos componentes personalizados

4. **Validação**: Server-side obrigatório (Form Requests), client-side com Zod

5. **Logs**: Usar channels específicos por entidade

6. **Segurança**: HTTPS em produção, rate limiting em auth

7. **Código em inglês**: Variáveis, funções, classes, comments

8. **Docs em português**: READMEs, guias

9. **MVP primeiro**: Não implementar features que não são essenciais

10. **Laravel 12**: Atenção às breaking changes (bootstrap/app.php, CORS)

---

## PRÓXIMOS PASSOS

Quando receber o comando **"começar"** ou **"start"**, implementar nesta ordem:

1. **Controllers de Auth** (Login, Register, Logout, Me)
2. **StudentController** (CRUD completo)
3. **WorkoutController** (CRUD completo)
4. **Form Requests** (validações)
5. **API Resources** (transformers)
6. **Rotas** (routes/api.php)
7. **Mobile**: Auth screens + theme store
8. **Mobile**: Students screens
9. **Mobile**: Workouts screens
10. **Testes** e **ajustes finais**

---

**FIM DO CONTEXTO**
