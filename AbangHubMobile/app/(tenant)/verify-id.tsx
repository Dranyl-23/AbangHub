import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image, Alert, ActivityIndicator, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import * as SecureStore from 'expo-secure-store';
import * as ImagePicker from 'expo-image-picker';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function VerifyIdScreen() {
  const { isDarkMode } = useTheme();
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [uploading, setUploading] = useState(false);

  const pickImage = async () => {
    const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (permissionResult.granted === false) {
      Alert.alert('Permission Denied', 'Camera roll permissions are required.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: false,
      quality: 0.8,
    });

    if (!result.canceled && result.assets && result.assets.length > 0) {
      setImageUri(result.assets[0].uri);
    }
  };

  const uploadId = async () => {
    if (!imageUri) {
      Alert.alert('Error', 'Please select an image first.');
      return;
    }

    setUploading(true);
    try {
      const formData = new FormData();
      const filename = imageUri.split('/').pop() || 'id.jpg';
      const match = /\.(\w+)$/.exec(filename);
      const type = match ? `image/${match[1]}` : `image`;

      formData.append('id_picture', {
        uri: imageUri,
        name: filename,
        type,
      } as any);

      const token = await SecureStore.getItemAsync('userToken');
      const response = await apiClient.post('/profile/id', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${token}`
        },
      });

      const updatedUser = response.data.user;
      await SecureStore.setItemAsync('userData', JSON.stringify(updatedUser));
      
      Alert.alert('Success', 'Your ID has been submitted and is pending verification by the admin.', [
        { text: 'OK', onPress: () => router.push('/(tenant)/profile' as any) }
      ]);
    } catch (error: any) {
      console.error(error);
      Alert.alert('Error', error.response?.data?.message || 'Failed to upload ID.');
    } finally {
      setUploading(false);
    }
  };

  return (
    <ScrollView style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <TouchableOpacity onPress={() => router.push('/(tenant)/profile' as any)} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? "#f8fafc" : "#0f172a"} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Verify Identity</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.content}>
        <View style={[styles.infoBox, isDarkMode && styles.infoBoxDark]}>
          <Ionicons name="shield-checkmark" size={40} color={isDarkMode ? "#22c55e" : "#15803d"} />
          <Text style={[styles.infoTitle, isDarkMode && styles.infoTitleDark]}>Why do we need this?</Text>
          <Text style={[styles.infoText, isDarkMode && styles.infoTextDark]}>
            To ensure a safe community, Landlords require tenants to be verified. Please upload a clear photo of any Government-issued ID (Passport, Driver's License, UMID, etc.).
          </Text>
        </View>

        <TouchableOpacity style={[styles.imagePicker, isDarkMode && styles.imagePickerDark]} onPress={pickImage}>
          {imageUri ? (
            <Image source={{ uri: imageUri }} style={styles.selectedImage} />
          ) : (
            <View style={styles.placeholder}>
              <Ionicons name="images-outline" size={48} color={isDarkMode ? "#cbd5e1" : "#94a3b8"} />
              <Text style={[styles.placeholderText, isDarkMode && styles.textDark]}>Tap to select ID Photo</Text>
            </View>
          )}
        </TouchableOpacity>

        {imageUri && (
          <TouchableOpacity 
            style={styles.submitButton} 
            onPress={uploadId}
            disabled={uploading}
          >
            {uploading ? (
              <ActivityIndicator color="#ffffff" />
            ) : (
              <Text style={styles.submitButtonText}>Submit for Verification</Text>
            )}
          </TouchableOpacity>
        )}
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
  content: { padding: 24 },
  infoBox: {
    backgroundColor: '#dcfce7', padding: 20, borderRadius: 16, alignItems: 'center', marginBottom: 24,
  },
  infoBoxDark: { backgroundColor: 'rgba(21, 128, 61, 0.2)' },
  infoTitle: { fontSize: 18, fontWeight: 'bold', color: '#15803d', marginTop: 12, marginBottom: 8 },
  infoTitleDark: { color: '#4ade80' },
  infoText: { fontSize: 14, color: '#166534', textAlign: 'center', lineHeight: 20 },
  infoTextDark: { color: '#86efac' },
  imagePicker: {
    backgroundColor: '#ffffff', borderWidth: 2, borderColor: '#e2e8f0', borderStyle: 'dashed',
    borderRadius: 16, height: 250, overflow: 'hidden', marginBottom: 24,
  },
  imagePickerDark: {
    backgroundColor: '#1e293b', borderColor: '#475569',
  },
  placeholder: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  placeholderText: { marginTop: 12, fontSize: 16, color: '#64748b', fontWeight: '500' },
  selectedImage: { width: '100%', height: '100%', resizeMode: 'cover' },
  submitButton: {
    backgroundColor: '#e11d48', padding: 16, borderRadius: 12, alignItems: 'center',
    shadowColor: '#e11d48', shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2, shadowRadius: 8, elevation: 4,
  },
  submitButtonText: { color: '#ffffff', fontSize: 18, fontWeight: 'bold' },
});
