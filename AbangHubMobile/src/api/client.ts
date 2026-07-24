import axios, { InternalAxiosRequestConfig } from 'axios';
import * as SecureStore from 'expo-secure-store';
import { router } from 'expo-router';

// NOTE: For Android Emulator, use 10.0.2.2 instead of localhost.
// Changed to your actual local IP so your physical phone can connect to the PC!
const BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'http://192.168.100.14:8000/api';

const apiClient = axios.create({
  baseURL: BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Interceptor to automatically add the token to every request
apiClient.interceptors.request.use(
  async (config: InternalAxiosRequestConfig) => {
    try {
      const token = await SecureStore.getItemAsync('userToken');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error('Error fetching token from SecureStore', error);
    }
    return config;
  },
  (error: any) => {
    return Promise.reject(error);
  }
);

// Interceptor to handle 401 Unauthorized globally
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response && error.response.status === 401) {
      await SecureStore.deleteItemAsync('userToken');
      await SecureStore.deleteItemAsync('userData');
      router.replace('/landing' as any);
    }
    return Promise.reject(error);
  }
);

export default apiClient;
