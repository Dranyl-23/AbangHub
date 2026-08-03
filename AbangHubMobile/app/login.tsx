import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ActivityIndicator, Alert, Image } from 'react-native';
import { router } from 'expo-router';
import * as WebBrowser from 'expo-web-browser';
import { makeRedirectUri } from 'expo-auth-session';
import { Ionicons } from '@expo/vector-icons';
import apiClient from '../src/api/client';
import { useAuth } from '../src/context/AuthContext';
import { supabase } from '../src/lib/supabase';

WebBrowser.maybeCompleteAuthSession();

export default function LoginScreen() {
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(false);
  const [showPassword, setShowPassword] = useState<boolean>(false);

  const { login } = useAuth();

  /**
   * Supabase Google OAuth flow:
   * 1. Ask Supabase for the Google OAuth URL
   * 2. Open it in an in-app browser (WebBrowser)
   * 3. Supabase redirects back to our app with a session in the URL
   * 4. Extract the Supabase access_token from the session
   * 5. Send it to our Laravel backend to get a Sanctum token
   */
  const handleGoogleLogin = async () => {
    setLoading(true);
    try {
      // Build the deep-link redirect URI that Supabase will redirect back to.
      // In Expo Go: exp://... | In standalone: abanghubmobile://...
      const redirectUrl = makeRedirectUri({
        scheme: 'abanghubmobile',
        path: 'auth/callback',
      });

      // Step 1: Get the Supabase-generated Google OAuth URL
      const { data, error } = await supabase.auth.signInWithOAuth({
        provider: 'google',
        options: {
          redirectTo: redirectUrl,
          skipBrowserRedirect: true, // We control the browser ourselves
        },
      });

      if (error || !data?.url) {
        throw new Error(error?.message ?? 'Failed to start Google login.');
      }

      // Step 2: Open Google login in the in-app browser
      const result = await WebBrowser.openAuthSessionAsync(data.url, redirectUrl);

      if (result.type !== 'success') {
        // User cancelled or browser closed — not an error
        return;
      }

      // Step 3: Extract access_token and refresh_token from redirect URL hash/query
      const urlParts = result.url.split('#')[1] || result.url.split('?')[1] || '';
      const urlParams = new URLSearchParams(urlParts);
      const supabaseAccessToken = urlParams.get('access_token');
      const refreshToken = urlParams.get('refresh_token');

      if (!supabaseAccessToken) {
        throw new Error('Could not retrieve access token from Google session.');
      }

      if (refreshToken) {
        await supabase.auth.setSession({
          access_token: supabaseAccessToken,
          refresh_token: refreshToken,
        });
      }

      // Step 4: Send the verified Supabase token to our Laravel backend.
      // Laravel will call the Supabase /auth/v1/user endpoint to verify it,
      // then find/create the local user and return a Sanctum token.
      const apiResponse = await apiClient.post('/auth/supabase', {
        supabase_token: supabaseAccessToken,
      });

      const { token, user } = apiResponse.data;
      await login(token, user);

      Alert.alert('Success', 'Logged in via Google!');
    } catch (error: any) {
      console.error('Google login error:', error);
      const msg = error.response?.data?.message ?? error.message ?? 'Google login failed.';
      Alert.alert('Error', msg);
    } finally {
      setLoading(false);
    }
  };

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Error', 'Please enter both email and password.');
      return;
    }

    setLoading(true);
    try {
      const response = await apiClient.post('/login', { email, password });
      
      const { token, user } = response.data;
      
      await login(token, user);
      Alert.alert('Success', 'Logged in successfully!');
    } catch (error: any) {
      const message = error.response?.data?.message || 'Login failed.';
      Alert.alert('Login Error', message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Welcome to AbangHub</Text>
      <Text style={styles.subtitle}>Sign in to your account</Text>

      <View style={styles.inputContainer}>
        <TextInput
          style={styles.input}
          placeholder="Email address"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          autoCapitalize="none"
        />
        <View style={styles.passwordContainer}>
          <TextInput
            style={styles.passwordInput}
            placeholder="Password"
            placeholderTextColor="#94a3b8"
            value={password}
            onChangeText={setPassword}
            secureTextEntry={!showPassword}
          />
          <TouchableOpacity onPress={() => setShowPassword(!showPassword)} style={{ padding: 10 }}>
            <Ionicons name={showPassword ? "eye-off" : "eye"} size={24} color="#94a3b8" />
          </TouchableOpacity>
        </View>
      </View>

      <TouchableOpacity 
        style={styles.button} 
        onPress={handleLogin}
        disabled={loading}
      >
        {loading ? (
          <ActivityIndicator color="#fff" />
        ) : (
          <Text style={styles.buttonText}>Sign In</Text>
        )}
      </TouchableOpacity>

      <View style={styles.registerContainer}>
        <Text style={styles.registerText}>Don't have an account? </Text>
        <TouchableOpacity onPress={() => router.push('/register' as any)}>
          <Text style={styles.registerLink}>Register</Text>
        </TouchableOpacity>
      </View>

      <View style={styles.dividerContainer}>
        <View style={styles.divider} />
        <Text style={styles.dividerText}>OR</Text>
        <View style={styles.divider} />
      </View>

      {/* Google Login Button — powered by Supabase OAuth */}
      <TouchableOpacity
        style={styles.googleButton}
        onPress={handleGoogleLogin}
        disabled={loading}
      >
        <Image
          source={require('../assets/images/google.png')}
          style={styles.googleIcon}
        />
        <Text style={styles.googleButtonText}>Continue with Google</Text>
      </TouchableOpacity>
      
      <TouchableOpacity style={styles.secondaryButton} onPress={() => router.push('/register' as any)}>
        <Text style={styles.secondaryButtonText}>Don't have an account? Sign up</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 24,
    justifyContent: 'center',
    backgroundColor: '#f8fafc',
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 8,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 16,
    color: '#64748b',
    marginBottom: 32,
    textAlign: 'center',
  },
  inputContainer: {
    marginBottom: 24,
  },
  input: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
    fontSize: 16,
    color: '#0f172a',
  },
  passwordContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    marginBottom: 16,
    paddingRight: 16,
  },
  passwordInput: {
    flex: 1,
    padding: 16,
    fontSize: 16,
    color: '#0f172a',
  },
  button: {
    backgroundColor: '#e11d48',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 16,
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  buttonText: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  dividerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  divider: {
    flex: 1,
    height: 1,
    backgroundColor: '#e2e8f0',
  },
  dividerText: {
    paddingHorizontal: 16,
    color: '#94a3b8',
    fontWeight: '600',
  },
  googleButton: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
    shadowColor: '#94a3b8',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  googleIcon: {
    width: 24,
    height: 24,
    marginRight: 12,
  },
  googleButtonText: {
    color: '#334155',
    fontSize: 16,
    fontWeight: 'bold',
  },
  secondaryButton: {
    alignItems: 'center',
    padding: 8,
  },
  secondaryButtonText: {
    color: '#475569',
    fontSize: 16,
  },
  registerContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginBottom: 24,
  },
  registerText: {
    color: '#64748b',
    fontSize: 15,
  },
  registerLink: {
    color: '#e11d48',
    fontSize: 15,
    fontWeight: 'bold',
  },
});
