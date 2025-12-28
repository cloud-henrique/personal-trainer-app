import { useMutation, useQuery, useQueryClient, useInfiniteQuery } from '@tanstack/react-query';
import { api, ApiResponse } from './api';
import type {
  Student,
  StudentFormData,
  StudentListParams,
  PaginatedStudentsResponse
} from '../types/student';

// ========================================
// API Functions
// ========================================

const fetchStudents = async (params: StudentListParams = {}): Promise<PaginatedStudentsResponse> => {
  const { data } = await api.get<ApiResponse<PaginatedStudentsResponse>>('/students', { params });
  return data.data!;
};

const fetchStudent = async (id: number): Promise<Student> => {
  const { data } = await api.get<ApiResponse<Student>>(`/students/${id}`);
  return data.data!;
};

const createStudent = async (studentData: StudentFormData): Promise<Student> => {
  // Converter Date para string no formato dd/mm/yyyy se necessário
  const formattedData = {
    ...studentData,
    birth_date: studentData.birth_date
      ? formatDateToBR(studentData.birth_date)
      : null,
  };

  const { data } = await api.post<ApiResponse<Student>>('/students', formattedData);
  return data.data!;
};

const updateStudent = async (id: number, studentData: StudentFormData): Promise<Student> => {
  // Converter Date para string no formato dd/mm/yyyy se necessário
  const formattedData = {
    ...studentData,
    birth_date: studentData.birth_date
      ? formatDateToBR(studentData.birth_date)
      : null,
  };

  const { data } = await api.put<ApiResponse<Student>>(`/students/${id}`, formattedData);
  return data.data!;
};

const deleteStudent = async (id: number): Promise<void> => {
  await api.delete(`/students/${id}`);
};

// ========================================
// Helper Functions
// ========================================

const formatDateToBR = (date: Date): string => {
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

export const parseBRDate = (dateString: string): Date | null => {
  if (!dateString) return null;
  const [day, month, year] = dateString.split('/');
  if (!day || !month || !year) return null;
  return new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
};

// ========================================
// React Query Hooks
// ========================================

/**
 * Hook para listar alunos com paginação infinita
 */
export const useStudents = (params: StudentListParams = {}) => {
  return useInfiniteQuery({
    queryKey: ['students', params],
    queryFn: ({ pageParam = 1 }) => fetchStudents({ ...params, page: pageParam }),
    getNextPageParam: (lastPage) => {
      if (lastPage.current_page < lastPage.last_page) {
        return lastPage.current_page + 1;
      }
      return undefined;
    },
    initialPageParam: 1,
    staleTime: 1000 * 60 * 5, // 5 minutos
  });
};

/**
 * Hook para buscar um aluno específico
 */
export const useStudent = (id: number | undefined) => {
  return useQuery({
    queryKey: ['students', id],
    queryFn: () => fetchStudent(id!),
    enabled: !!id,
    staleTime: 1000 * 60 * 5, // 5 minutos
  });
};

/**
 * Hook para criar um novo aluno
 */
export const useCreateStudent = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: createStudent,
    onSuccess: () => {
      // Invalida todas as queries de lista de students
      queryClient.invalidateQueries({ queryKey: ['students'] });
    },
  });
};

/**
 * Hook para atualizar um aluno existente
 */
export const useUpdateStudent = (id: number) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (studentData: StudentFormData) => updateStudent(id, studentData),
    onSuccess: () => {
      // Invalida a lista de students e o student específico
      queryClient.invalidateQueries({ queryKey: ['students'] });
      queryClient.invalidateQueries({ queryKey: ['students', id] });
    },
  });
};

/**
 * Hook para deletar um aluno
 */
export const useDeleteStudent = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: deleteStudent,
    onSuccess: () => {
      // Invalida todas as queries de students
      queryClient.invalidateQueries({ queryKey: ['students'] });
    },
  });
};
