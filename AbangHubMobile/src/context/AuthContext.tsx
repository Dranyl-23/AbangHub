import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import * as SecureStore from 'expo-secure-store';
import { router } from 'expo-router';

// Proper User type — no more 'any'!
export interface User {
  id: number;
  username: string;
  email: string;
  user_type: 'admin' | 'landlord' | 'tenant';
  full_name?: string | null;
  phone?: string | null;
  profile_image?: string | null;
  id_picture?: string | null;
  is_verified?: boolean;
  is_banned?: boolean;
  emergency_contact_name?: string | null;
  emergency_contact_phone?: string | null;
  emergency_contact_relationship?: string | null;
  created_at?: string;
  updated_at?: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (token: string, userData: User) => Promise<void>;
  logout: () => Promise<void>;
  updateUser: (userData: Partial<User>) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadUser();
  }, []);

  const loadUser = async () => {
    try {
      const userDataStr = await SecureStore.getItemAsync('userData');
      if (userDataStr) {
        setUser(JSON.parse(userDataStr) as User);
      }
    } catch (error) {
      console.error('Error loading user data', error);
    } finally {
      setLoading(false);
    }
  };

  const login = async (token: string, userData: User) => {
    await SecureStore.setItemAsync('userToken', token);
    await SecureStore.setItemAsync('userData', JSON.stringify(userData));
    setUser(userData);

    if (userData.user_type === 'landlord') {
      router.replace('/(landlord)/dashboard');
    } else {
      router.replace('/(tenant)/explore');
    }
  };

  const logout = async () => {
    await SecureStore.deleteItemAsync('userToken');
    await SecureStore.deleteItemAsync('userData');
    setUser(null);
    router.replace('/landing' as never);
  };

  // Utility to update user fields locally (e.g. after profile update)
  const updateUser = (userData: Partial<User>) => {
    setUser((prev) => {
      if (!prev) return null;
      const updated = { ...prev, ...userData };
      SecureStore.setItemAsync('userData', JSON.stringify(updated)).catch(console.error);
      return updated;
    });
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, updateUser }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
