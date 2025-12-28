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
export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
};

export type AppStackParamList = {
  Dashboard: undefined;
  StudentList: undefined;
  StudentDetail: { studentId: number };
  StudentForm: { studentId?: number; mode: 'create' | 'edit' };
};

const AuthStack = createNativeStackNavigator<AuthStackParamList>();
const AppStack = createNativeStackNavigator<AppStackParamList>();

export default function AppNavigator() {
  const { isAuthenticated } = useAuthStore();

  return (
    <NavigationContainer>
      {!isAuthenticated ? (
        // Auth Stack
        <AuthStack.Navigator screenOptions={{ headerShown: false }}>
          <AuthStack.Screen name='Login' component={LoginScreen} />
          <AuthStack.Screen name='Register' component={RegisterScreen} />
        </AuthStack.Navigator>
      ) : (
        // Authenticated Stack
        <AppStack.Navigator screenOptions={{ headerShown: false }}>
          <AppStack.Screen name='Dashboard' component={DashboardScreen} />
          <AppStack.Screen name='StudentList' component={StudentListScreen} />
          <AppStack.Screen name='StudentDetail' component={StudentDetailScreen} />
          <AppStack.Screen name='StudentForm' component={StudentFormScreen} />
        </AppStack.Navigator>
      )}
    </NavigationContainer>
  );
}
