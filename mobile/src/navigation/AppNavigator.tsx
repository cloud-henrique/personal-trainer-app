import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuthStore } from '../stores/authStore';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';

// Trainer Screens
import DashboardScreen from '../screens/trainer/DashboardScreen';
import StudentListScreen from '../screens/trainer/StudentListScreen';
import StudentDetailScreen from '../screens/trainer/StudentDetailScreen';
import StudentFormScreen from '../screens/trainer/StudentFormScreen';

// Type definitions for navigation
export type AppStackParamList = {
  Dashboard: undefined;
  StudentList: undefined;
  StudentDetail: { studentId: number };
  StudentForm: { studentId?: number; mode: 'create' | 'edit' };
};

const Stack = createNativeStackNavigator<AppStackParamList>();

export default function AppNavigator() {
  const { isAuthenticated } = useAuthStore();

  return (
    <NavigationContainer>
      {!isAuthenticated ? (
        // Auth Stack
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          <Stack.Screen name='Login' component={LoginScreen} />
          <Stack.Screen name='Register' component={RegisterScreen} />
        </Stack.Navigator>
      ) : (
        // Authenticated Stack
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          <Stack.Screen name='Dashboard' component={DashboardScreen} />
          <Stack.Screen name='StudentList' component={StudentListScreen} />
          <Stack.Screen name='StudentDetail' component={StudentDetailScreen} />
          <Stack.Screen name='StudentForm' component={StudentFormScreen} />
        </Stack.Navigator>
      )}
    </NavigationContainer>
  );
}
