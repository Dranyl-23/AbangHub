import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { useTheme } from '../../src/context/ThemeContext';
import React from 'react';
import { useUnreadMessages } from '../../src/hooks/useUnreadMessages';

export default function LandlordLayout() {
  const { isDarkMode } = useTheme();
  const unreadCount = useUnreadMessages();

  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: '#e11d48', // Rose color for active tab
        tabBarInactiveTintColor: isDarkMode ? '#64748b' : '#94a3b8',
        tabBarStyle: {
          backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
          borderTopColor: isDarkMode ? '#334155' : '#e2e8f0',
          elevation: 5,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -2 },
          shadowOpacity: 0.1,
          shadowRadius: 4,
          height: 85,
          paddingBottom: 24,
          paddingTop: 8,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '500',
        },
      }}
    >
      <Tabs.Screen
        name="dashboard"
        options={{
          title: 'Dashboard',
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="pie-chart-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="properties"
        options={{
          title: 'My Properties',
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="home-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="applications"
        options={{
          href: null,
          title: 'Applications',
        }}
      />
      <Tabs.Screen
        name="maintenance"
        options={{
          href: null,
          title: 'Maintenance',
        }}
      />
      <Tabs.Screen
        name="tenants"
        options={{
          href: null,
          title: 'Active Tenants',
        }}
      />
      <Tabs.Screen
        name="invoices"
        options={{
          href: null,
          title: 'Invoices',
        }}
      />
      <Tabs.Screen
        name="messages"
        options={{
          title: 'Messages',
          tabBarBadge: unreadCount > 0 ? unreadCount : undefined,
          tabBarBadgeStyle: { backgroundColor: '#e11d48' },
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="chatbubble-ellipses-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profile',
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="person-outline" size={size} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="add-property"
        options={{
          href: null, // Hide from tab bar
        }}
      />
      <Tabs.Screen
        name="expenses"
        options={{
          href: null, // Hide from tab bar
        }}
      />
      <Tabs.Screen
        name="edit-property/[id]"
        options={{
          href: null, // Hide from tab bar
        }}
      />
      <Tabs.Screen
        name="settings"
        options={{ href: null }}
      />
      <Tabs.Screen
        name="verify-id"
        options={{ href: null }}
      />
      <Tabs.Screen
        name="change-password"
        options={{ href: null }}
      />
      <Tabs.Screen
        name="notifications"
        options={{ href: null }}
      />
      <Tabs.Screen
        name="help"
        options={{ href: null }}
      />
      <Tabs.Screen
        name="legal"
        options={{ href: null }}
      />
    </Tabs>
  );
}
