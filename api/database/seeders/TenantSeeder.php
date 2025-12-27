<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Goal;
use App\Models\Student;
use App\Models\StudentMeasurement;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * TenantSeeder
 *
 * Seeds the database with test data for development.
 * Creates 2 tenants with complete data structure.
 */
class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ================================================
        // TENANT 1: Academia Fitness Pro
        // ================================================

        $tenant1 = Tenant::create([
            'name' => 'Academia Fitness Pro',
            'slug' => 'fitness-pro',
            'email' => 'contato@fitnesspro.com',
            'phone' => '(11) 98765-4321',
            'primary_color' => '#FF6B6B',
            'plan' => 'free',
            'is_active' => true,
        ]);

        // Admin User (Personal Trainer)
        $trainer1 = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Carlos Silva',
            'email' => 'carlos@fitnesspro.com',
            'password' => Hash::make('password123'),
            'phone' => '(11) 98765-4321',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Students for Tenant 1
        $student1 = Student::create([
            'tenant_id' => $tenant1->id,
            'name' => 'João Pedro Santos',
            'email' => 'joao@email.com',
            'phone' => '(11) 91234-5678',
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'height' => 178.00,
            'trainer_id' => $trainer1->id,
            'is_active' => true,
        ]);

        $student2 = Student::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Maria Fernanda Costa',
            'email' => 'maria@email.com',
            'phone' => '(11) 92345-6789',
            'birth_date' => '1992-08-22',
            'gender' => 'female',
            'height' => 165.00,
            'medical_conditions' => 'Hipertensão controlada',
            'trainer_id' => $trainer1->id,
            'is_active' => true,
        ]);

        $student3 = Student::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Ricardo Oliveira',
            'email' => 'ricardo@email.com',
            'phone' => '(11) 93456-7890',
            'birth_date' => '1988-12-10',
            'gender' => 'male',
            'height' => 182.00,
            'trainer_id' => $trainer1->id,
            'is_active' => true,
        ]);

        // Measurements for Student 1
        StudentMeasurement::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student1->id,
            'weight' => 82.50,
            'body_fat' => 18.50,
            'muscle_mass' => 35.20,
            'chest' => 98.00,
            'waist' => 85.00,
            'hips' => 95.00,
            'right_arm' => 35.00,
            'left_arm' => 34.50,
            'measured_at' => now()->subDays(30),
        ]);

        StudentMeasurement::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student1->id,
            'weight' => 80.00,
            'body_fat' => 16.80,
            'muscle_mass' => 36.50,
            'chest' => 100.00,
            'waist' => 82.00,
            'hips' => 94.00,
            'right_arm' => 36.00,
            'left_arm' => 35.50,
            'measured_at' => now(),
        ]);

        // Measurements for Student 2
        StudentMeasurement::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student2->id,
            'weight' => 65.00,
            'body_fat' => 22.00,
            'muscle_mass' => 28.00,
            'waist' => 68.00,
            'hips' => 92.00,
            'measured_at' => now()->subDays(15),
        ]);

        // Workout for Student 1
        $workout1 = Workout::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student1->id,
            'created_by' => $trainer1->id,
            'name' => 'Treino de Hipertrofia - Peito e Tríceps',
            'description' => 'Foco em ganho de massa muscular para peito e tríceps',
            'category' => 'strength',
            'starts_at' => now(),
            'is_active' => true,
        ]);

        // Exercises for Workout 1
        Exercise::create([
            'tenant_id' => $tenant1->id,
            'workout_id' => $workout1->id,
            'order' => 1,
            'name' => 'Supino Reto',
            'muscle_group' => 'Peitoral',
            'sets' => 4,
            'reps' => '8-10',
            'rest' => '90s',
            'load' => '80kg',
        ]);

        Exercise::create([
            'tenant_id' => $tenant1->id,
            'workout_id' => $workout1->id,
            'order' => 2,
            'name' => 'Supino Inclinado',
            'muscle_group' => 'Peitoral Superior',
            'sets' => 3,
            'reps' => '10-12',
            'rest' => '60s',
            'load' => '60kg',
        ]);

        Exercise::create([
            'tenant_id' => $tenant1->id,
            'workout_id' => $workout1->id,
            'order' => 3,
            'name' => 'Tríceps Testa',
            'muscle_group' => 'Tríceps',
            'sets' => 3,
            'reps' => '12-15',
            'rest' => '60s',
            'load' => '30kg',
        ]);

        // Workout for Student 2
        $workout2 = Workout::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student2->id,
            'created_by' => $trainer1->id,
            'name' => 'Treino de Definição - Pernas',
            'description' => 'Treino focado em tonificação e definição de membros inferiores',
            'category' => 'strength',
            'starts_at' => now(),
            'is_active' => true,
        ]);

        Exercise::create([
            'tenant_id' => $tenant1->id,
            'workout_id' => $workout2->id,
            'order' => 1,
            'name' => 'Agachamento Livre',
            'muscle_group' => 'Quadríceps',
            'sets' => 4,
            'reps' => '12-15',
            'rest' => '90s',
            'load' => '40kg',
        ]);

        Exercise::create([
            'tenant_id' => $tenant1->id,
            'workout_id' => $workout2->id,
            'order' => 2,
            'name' => 'Leg Press 45°',
            'muscle_group' => 'Quadríceps',
            'sets' => 3,
            'reps' => '15-20',
            'rest' => '60s',
            'load' => '120kg',
        ]);

        // Goals for Student 1
        Goal::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student1->id,
            'title' => 'Ganhar 5kg de Massa Muscular',
            'description' => 'Meta de ganho de massa magra em 6 meses',
            'type' => 'muscle_gain',
            'target_value' => 87.50,
            'current_value' => 80.00,
            'unit' => 'kg',
            'starts_at' => now(),
            'target_date' => now()->addMonths(6),
            'status' => 'active',
        ]);

        Goal::create([
            'tenant_id' => $tenant1->id,
            'student_id' => $student2->id,
            'title' => 'Perder 8kg',
            'description' => 'Meta de emagrecimento saudável',
            'type' => 'weight_loss',
            'target_value' => 57.00,
            'current_value' => 65.00,
            'unit' => 'kg',
            'starts_at' => now(),
            'target_date' => now()->addMonths(4),
            'status' => 'active',
        ]);

        // ================================================
        // TENANT 2: Personal Strong
        // ================================================

        $tenant2 = Tenant::create([
            'name' => 'Personal Strong',
            'slug' => 'personal-strong',
            'email' => 'contato@personalstrong.com',
            'phone' => '(21) 97654-3210',
            'primary_color' => '#4ECDC4',
            'plan' => 'free',
            'is_active' => true,
        ]);

        $trainer2 = User::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Ana Paula Rodrigues',
            'email' => 'ana@personalstrong.com',
            'password' => Hash::make('password123'),
            'phone' => '(21) 97654-3210',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Students for Tenant 2
        $student4 = Student::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Pedro Henrique Lima',
            'email' => 'pedro@email.com',
            'phone' => '(21) 98765-1234',
            'birth_date' => '1990-03-25',
            'gender' => 'male',
            'height' => 175.00,
            'trainer_id' => $trainer2->id,
            'is_active' => true,
        ]);

        $student5 = Student::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Juliana Mendes',
            'email' => 'juliana@email.com',
            'phone' => '(21) 99876-5432',
            'birth_date' => '1993-07-18',
            'gender' => 'female',
            'height' => 168.00,
            'trainer_id' => $trainer2->id,
            'is_active' => true,
        ]);

        // Workout for Student 4
        $workout3 = Workout::create([
            'tenant_id' => $tenant2->id,
            'student_id' => $student4->id,
            'created_by' => $trainer2->id,
            'name' => 'Treino Full Body',
            'description' => 'Treino completo para corpo inteiro',
            'category' => 'mixed',
            'starts_at' => now(),
            'is_active' => true,
        ]);

        Exercise::create([
            'tenant_id' => $tenant2->id,
            'workout_id' => $workout3->id,
            'order' => 1,
            'name' => 'Burpee',
            'muscle_group' => 'Corpo Inteiro',
            'sets' => 3,
            'reps' => '15',
            'rest' => '45s',
        ]);

        $this->command->info('✅ Seeders executados com sucesso!');
        $this->command->info('');
        $this->command->info('📊 Dados criados:');
        $this->command->info('   - 2 Tenants (Personal Trainers)');
        $this->command->info('   - 2 Admins/Trainers');
        $this->command->info('   - 5 Students (Alunos)');
        $this->command->info('   - 3 Measurements');
        $this->command->info('   - 3 Workouts');
        $this->command->info('   - 7 Exercises');
        $this->command->info('   - 2 Goals');
        $this->command->info('');
        $this->command->info('🔑 Credenciais de Teste:');
        $this->command->info('');
        $this->command->info('   Tenant 1 - Academia Fitness Pro');
        $this->command->info('   Email: carlos@fitnesspro.com');
        $this->command->info('   Senha: password123');
        $this->command->info('');
        $this->command->info('   Tenant 2 - Personal Strong');
        $this->command->info('   Email: ana@personalstrong.com');
        $this->command->info('   Senha: password123');
    }
}
