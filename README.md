# Personal Trainer App - White Label

Sistema completo para personal trainers gerenciarem alunos, treinos e acompanhamento.

## 🏗️ Estrutura
```
personal-trainer-app/
├── api/          # Laravel backend
├── mobile/       # React Native (Expo)
└── shared/       # Types e constantes compartilhadas
```

## 🚀 Setup

### Backend (Laravel)
```bash
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Mobile (React Native)
```bash
cd mobile
npm install
npx expo start
```

## 📝 Documentação

- [Backend API](./api/README.md)
- [Mobile App](./mobile/README.md)
