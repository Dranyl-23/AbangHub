import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import * as SecureStore from 'expo-secure-store';
import apiClient from '../../src/api/client';
import { User } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function SettingsScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(false);
  const [fullName, setFullName] = useState('');
  const [phone, setPhone] = useState('');
  const [emergencyName, setEmergencyName] = useState('');
  const [emergencyPhone, setEmergencyPhone] = useState('');

  useEffect(() => {
    loadUserData();
  }, []);

  const loadUserData = async () => {
    try {
      const userDataStr = await SecureStore.getItemAsync('userData');
      if (userDataStr) {
        const user: User = JSON.parse(userDataStr);
        setFullName(user.full_name || user.username || '');
        setPhone(user.phone || '');
        // @ts-ignore - emergency contacts exist in DB now
        setEmergencyName(user.emergency_contact_name || '');
        // @ts-ignore
        setEmergencyPhone(user.emergency_contact_phone || '');
      }
    } catch (e) {
      console.error(e);
    }
  };

  const handleSave = async () => {
    setLoading(true);
    try {
      const token = await SecureStore.getItemAsync('userToken');
      const response = await apiClient.put('/profile', {
        full_name: fullName,
        phone,
        emergency_contact_name: emergencyName,
        emergency_contact_phone: emergencyPhone
      }, {
        headers: { Authorization: `Bearer ${token}` }
      });

      const updatedUser = response.data.user;
      await SecureStore.setItemAsync('userData', JSON.stringify(updatedUser));
      
      Alert.alert('Success', 'Account settings saved!');
      router.push('/(landlord)/profile' as any);
    } catch (error: any) {
      console.error(error);
      Alert.alert('Error', error.response?.data?.message || 'Failed to save settings.');
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
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Account Settings</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.form}>
        <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Personal Information</Text>
        
        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Full Name</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="e.g. Juan Dela Cruz"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          value={fullName}
          onChangeText={setFullName}
        />

        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Phone Number</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="e.g. 09123456789"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          value={phone}
          onChangeText={setPhone}
          keyboardType="phone-pad"
        />

        <Text style={[styles.sectionTitle, { marginTop: 24 }, isDarkMode && styles.textDark]}>Emergency Contact</Text>
        <Text style={[styles.description, isDarkMode && styles.descriptionDark]}>
          This person will be contacted if there is an emergency at your rented property.
        </Text>

        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Contact Name</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="e.g. Maria Dela Cruz"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          value={emergencyName}
          onChangeText={setEmergencyName}
        />

        <Text style={[styles.label, isDarkMode && styles.labelDark]}>Contact Phone Number</Text>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="e.g. 09987654321"
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          value={emergencyPhone}
          onChangeText={setEmergencyPhone}
          keyboardType="phone-pad"
        />

        <TouchableOpacity 
          style={styles.submitButton} 
          onPress={handleSave}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#ffffff" />
          ) : (
            <Text style={styles.submitButtonText}>Save Changes</Text>
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
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#0f172a', marginBottom: 16 },
  description: { fontSize: 14, color: '#64748b', marginBottom: 16, lineHeight: 20 },
  descriptionDark: { color: '#94a3b8' },
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
