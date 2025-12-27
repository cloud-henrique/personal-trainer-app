import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuthStore } from '../stores/authStore';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';

// Trainer Screens (placeholder)
import DashboardScreen from '../screens/trainer/DashboardScreen';

const Stack = createNativeStackNavigator();

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
        <Stack.Navigator>
          <Stack.Screen name='Dashboard' component={DashboardScreen} options={{ title: 'Dashboard' }} />
        </Stack.Navigator>
      )}
    </NavigationContainer>
  );
}
