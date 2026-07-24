import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import * as SecureStore from 'expo-secure-store';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function ChangePasswordScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(false);
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const handleChangePassword = async () => {
    if (!currentPassword || !newPassword || !confirmPassword) {
      Alert.alert('Error', 'Please fill in all fields.');
      return;
    }

    if (newPassword !== confirmPassword) {
      Alert.alert('Error', 'New passwords do not match.');
      return;
    }

    setLoading(true);
    try {
      const token = await SecureStore.getItemAsync('userToken');
      await apiClient.put('/profile/password', {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: confirmPassword
      }, {
        headers: { Authorization: `Bearer ${token}` }
      });
      
      Alert.alert('Success', 'Your password has been changed successfully.', [
        { text: 'OK', onPress: () => router.push('/(landlord)/profile' as any) }
      ]);
    } catch (error: any) {
      console.error(error);
      Alert.alert('Error', error.response?.data?.message || 'Failed to change password.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <TouchableOpacity onPress={() => router.push('/(landlord)/profile' as any)} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? "#f8fafc" : "#0f172a"} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Change Password</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.form}>
        <View style={[styles.infoBox, isDarkMode && styles.infoBoxDark]}>
          <Ionicons name="lock-closed" size={24} color={isDarkMode ? "#60a5fa" : "#0284c7"} />
          <Text style={[styles.infoText, isDarkMode && styles.infoTextDark]}>
            Your new password must be at least 8 characters long.
          </Text>
        </View>
        
        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Current Password</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="Enter current password"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          secureTextEntry
          value={currentPassword}
          onChangeText={setCurrentPassword}
        />

        <Text style={[styles.label, isDarkMode && styles.labelDark]}>New Password</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="Enter new password"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          secureTextEntry
          value={newPassword}
          onChangeText={setNewPassword}
        />

        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Confirm New Password</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="Re-type new password"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          secureTextEntry
          value={confirmPassword}
          onChangeText={setConfirmPassword}
        />

        <TouchableOpacity 
          style={styles.submitButton} 
          onPress={handleChangePassword}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#ffffff" />
          ) : (
            <Text style={styles.submitButtonText}>Update Password</Text>
          )}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  header: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingTop: 50, paddingBottom: 20, paddingHorizontal: 20, backgroundColor: '#ffffff',
    borderBottomWidth: 1, borderBottomColor: '#e2e8f0',
  },
  headerDark: { backgroundColor: '#1e293b', borderBottomColor: '#334155' },
  backButton: { padding: 8, marginLeft: -8 },
  headerTitle: { fontSize: 20, fontWeight: 'bold', color: '#0f172a' },
  textDark: { color: '#f8fafc' },
  form: { padding: 24 },
  infoBox: {
    flexDirection: 'row', backgroundColor: '#e0f2fe', padding: 16, borderRadius: 12, alignItems: 'center', marginBottom: 24, gap: 12
  },
  infoBoxDark: { backgroundColor: 'rgba(2, 132, 199, 0.2)' },
  infoText: { flex: 1, fontSize: 14, color: '#0369a1', lineHeight: 20 },
  infoTextDark: { color: '#7dd3fc' },
  label: { fontSize: 14, fontWeight: '600', color: '#334155', marginBottom: 8, textTransform: 'uppercase', letterSpacing: 0.5 },
  labelDark: { color: '#cbd5e1' },
  input: {
    backgroundColor: '#ffffff', borderWidth: 1, borderColor: '#e2e8f0',
    borderRadius: 12, padding: 16, marginBottom: 20, fontSize: 16, color: '#0f172a',
  },
  inputDark: {
    backgroundColor: '#1e293b', borderColor: '#334155', color: '#f8fafc',
  },
  submitButton: {
    backgroundColor: '#e11d48', padding: 16, borderRadius: 12, alignItems: 'center',
    marginTop: 12, shadowColor: '#e11d48', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2, shadowRadius: 8, elevation: 4,
  },
  submitButtonText: { color: '#ffffff', fontSize: 18, fontWeight: 'bold' },
});
