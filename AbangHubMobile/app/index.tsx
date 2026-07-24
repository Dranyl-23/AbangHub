import React, { useEffect } from 'react';
import { View, ActivityIndicator } from 'react-native';
import { router } from 'expo-router';
import { useAuth } from '../src/context/AuthContext';

export default function Index() {
  const { user, loading } = useAuth();

  useEffect(() => {
    if (!loading) {
      if (user) {
        if (user.user_type === 'landlord') {
          router.replace('/(landlord)/dashboard');
        } else {
          router.replace('/(tenant)/explore');
        }
      } else {
        router.replace('/landing' as any);
      }
    }
  }, [user, loading]);

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return null;
}
