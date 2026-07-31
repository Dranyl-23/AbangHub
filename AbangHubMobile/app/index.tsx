import React from 'react';
import { View, ActivityIndicator } from 'react-native';
import { Redirect } from 'expo-router';
import { useAuth } from '../src/context/AuthContext';

export default function Index() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#ffffff' }}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  if (user) {
    if (user.user_type === 'landlord') {
      return <Redirect href="/(landlord)/dashboard" />;
    } else {
      return <Redirect href="/(tenant)/explore" />;
    }
  }

  return <Redirect href="/landing" />;
}
