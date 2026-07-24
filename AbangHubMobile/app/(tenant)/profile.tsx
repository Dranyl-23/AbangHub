import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert, Image, ActivityIndicator, Switch, Modal, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';
import { router, useFocusEffect } from 'expo-router';
import * as ImagePicker from 'expo-image-picker';
import { User } from '../../src/types';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function ProfileScreen() {
  const [user, setUser] = useState<User | null>(null);
  const [uploading, setUploading] = useState(false);
  const [logoutModalVisible, setLogoutModalVisible] = useState(false);
  const { isDarkMode, toggleDarkMode } = useTheme();

  const loadUserData = async () => {
    try {
      const userData = await SecureStore.getItemAsync('userData');
      if (userData) {
        setUser(JSON.parse(userData));
      }
    } catch (e) {
      console.error(e);
    }
  };

  useFocusEffect(
    useCallback(() => {
      loadUserData();
    }, [])
  );

  const handleLogout = () => {
    setLogoutModalVisible(true);
  };

  const confirmLogout = async () => {
    try {
      await SecureStore.deleteItemAsync('userToken');
      await SecureStore.deleteItemAsync('userData');
      router.replace('/login' as any);
    } catch (error) {
      console.error('Error logging out:', error);
    }
  };

  const pickProfileImage = async () => {
    const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (permissionResult.granted === false) {
      Alert.alert('Permission Denied', 'Camera roll permissions are required.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [1, 1],
      quality: 0.8,
    });

    if (!result.canceled && result.assets && result.assets.length > 0) {
      uploadProfileImage(result.assets[0].uri);
    }
  };

  const uploadProfileImage = async (uri: string) => {
    setUploading(true);
    try {
      const formData = new FormData();
      const filename = uri.split('/').pop() || 'avatar.jpg';
      const match = /\.(\w+)$/.exec(filename);
      const type = match ? `image/${match[1]}` : `image`;

      formData.append('avatar', {
        uri,
        name: filename,
        type,
      } as any);

      const token = await SecureStore.getItemAsync('userToken');
      const response = await apiClient.post('/profile/avatar', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${token}`
        },
      });

      const updatedUser = response.data.user;
      setUser(updatedUser);
      await SecureStore.setItemAsync('userData', JSON.stringify(updatedUser));
      Alert.alert('Success', 'Profile picture updated!');
    } catch (error: any) {
      console.error(error);
      Alert.alert('Error', error.response?.data?.message || 'Failed to upload image.');
    } finally {
      setUploading(false);
    }
  };

  // Helper for full image URL if it's a relative path from Laravel
  const getAvatarUrl = (path: string | undefined) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseURL = apiClient.defaults.baseURL?.replace('/api', '');
    const formattedPath = path.startsWith('/') ? path : `/storage/${path}`;
    return `${baseURL}${formattedPath}`;
  };

  const avatarUrl = user?.avatar_url || getAvatarUrl(user?.profile_image);
  const isVerified = user?.is_verified;

  return (
    <ScrollView style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <TouchableOpacity style={styles.avatarContainer} onPress={pickProfileImage} disabled={uploading}>
          {uploading ? (
            <View style={styles.avatarPlaceholder}>
              <ActivityIndicator color="#e11d48" />
            </View>
          ) : avatarUrl ? (
            <Image source={{ uri: avatarUrl }} style={styles.avatarImage} />
          ) : (
            <View style={styles.avatarPlaceholder}>
              <Ionicons name="person" size={50} color="#cbd5e1" />
            </View>
          )}
          <View style={styles.cameraBadge}>
            <Ionicons name="camera" size={14} color="#ffffff" />
          </View>
        </TouchableOpacity>

        <Text style={[styles.name, isDarkMode && styles.textDark]}>
          {user?.full_name || user?.username || (user?.user_type === 'landlord' ? 'Landlord' : 'Tenant')}
        </Text>
        
        {/* Verification Status */}
        <View style={[styles.verificationBadge, isVerified ? styles.verified : styles.unverified]}>
          <Ionicons name={isVerified ? "checkmark-circle" : "alert-circle"} size={14} color={isVerified ? "#15803d" : "#c2410c"} />
          <Text style={[styles.verificationText, isVerified ? styles.verifiedText : styles.unverifiedText]}>
            {isVerified ? 'Verified Account' : 'Unverified Account'}
          </Text>
        </View>

        <Text style={[styles.email, isDarkMode && styles.textDarkMuted]}>{user?.email}</Text>
      </View>

      <View style={[styles.menu, isDarkMode && styles.menuDark]}>
        
        <View style={[styles.sectionHeader, isDarkMode && styles.sectionHeaderDark]}>
          <Text style={[styles.sectionHeaderText, isDarkMode && styles.sectionHeaderTextDark]}>Account & Security</Text>
        </View>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/settings' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="settings-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>Account Settings</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/verify-id' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="id-card-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>ID Verification</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/change-password' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="lock-closed-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>Change Password</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>

        <View style={[styles.sectionHeader, isDarkMode && styles.sectionHeaderDark]}>
          <Text style={[styles.sectionHeaderText, isDarkMode && styles.sectionHeaderTextDark]}>About & Support</Text>
        </View>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/wallet' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="wallet-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>My Wallet</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/help' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="help-circle-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>Help & Support</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>
        <TouchableOpacity style={[styles.menuItem, isDarkMode && styles.menuItemDark]} onPress={() => router.push('/(tenant)/legal' as any)}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="document-text-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>Terms & Privacy Policy</Text>
          <Ionicons name="chevron-forward" size={20} color="#cbd5e1" />
        </TouchableOpacity>

        <View style={[styles.sectionHeader, isDarkMode && styles.sectionHeaderDark]}>
          <Text style={[styles.sectionHeaderText, isDarkMode && styles.sectionHeaderTextDark]}>Preferences</Text>
        </View>
        <View style={[styles.menuItem, isDarkMode && styles.menuItemDark]}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="moon-outline" size={22} color={isDarkMode ? "#cbd5e1" : "#475569"} /></View>
          <Text style={[styles.menuText, isDarkMode && styles.textDark]}>Dark Mode</Text>
          <Switch
            trackColor={{ false: "#cbd5e1", true: "#e11d48" }}
            thumbColor={"#ffffff"}
            onValueChange={toggleDarkMode}
            value={isDarkMode}
          />
        </View>

        <TouchableOpacity style={[styles.menuItem, { borderBottomWidth: 0 }]} onPress={handleLogout}>
          <View style={[styles.menuIconContainer, isDarkMode && styles.menuIconContainerDark]}><Ionicons name="log-out-outline" size={22} color="#e11d48" /></View>
          <Text style={styles.logoutText}>Log Out</Text>
        </TouchableOpacity>

      </View>

      {/* Custom Logout Modal */}
      <Modal visible={logoutModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.modalContentDark]}>
            <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Log Out?</Text>
            <Text style={[styles.modalMessage, isDarkMode && styles.textDarkMuted]}>
              Are you sure you want to log out of your AbangHub account? You will need to login again to access your properties.
            </Text>
            <View style={styles.modalActions}>
              <TouchableOpacity style={[styles.modalButton, styles.modalButtonCancel, isDarkMode && styles.modalButtonCancelDark]} onPress={() => setLogoutModalVisible(false)}>
                <Text style={[styles.modalButtonTextCancel, isDarkMode && styles.textDark]}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalButton, styles.modalButtonConfirm]} onPress={confirmLogout}>
                <Text style={styles.modalButtonTextConfirm}>Yes, Log Out</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  header: {
    alignItems: 'center', padding: 32, paddingTop: 60, backgroundColor: '#ffffff',
    borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  headerDark: { backgroundColor: '#1e293b', borderBottomColor: '#334155' },
  avatarContainer: { position: 'relative', marginBottom: 16 },
  avatarPlaceholder: {
    width: 100, height: 100, borderRadius: 50, backgroundColor: '#f1f5f9',
    alignItems: 'center', justifyContent: 'center',
    borderWidth: 3, borderColor: '#ffffff', shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 10, elevation: 5
  },
  avatarImage: {
    width: 100, height: 100, borderRadius: 50,
    borderWidth: 3, borderColor: '#ffffff',
  },
  cameraBadge: {
    position: 'absolute', bottom: 0, right: 0, backgroundColor: '#e11d48',
    width: 32, height: 32, borderRadius: 16, alignItems: 'center', justifyContent: 'center',
    borderWidth: 2, borderColor: '#ffffff'
  },
  name: { fontSize: 24, fontWeight: 'bold', color: '#0f172a' },
  textDark: { color: '#f8fafc' },
  email: { fontSize: 14, color: '#64748b', marginTop: 12 },
  textDarkMuted: { color: '#94a3b8' },
  verificationBadge: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 16, marginTop: 8 },
  verified: { backgroundColor: '#dcfce7' },
  unverified: { backgroundColor: '#ffedd5' },
  verificationText: { fontSize: 12, fontWeight: 'bold', marginLeft: 4 },
  verifiedText: { color: '#15803d' },
  unverifiedText: { color: '#c2410c' },
  menu: { backgroundColor: '#ffffff', borderBottomWidth: 1, borderColor: '#f1f5f9' },
  menuDark: { backgroundColor: '#1e293b', borderColor: '#334155' },
  menuItem: { flexDirection: 'row', alignItems: 'center', padding: 16, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  menuItemDark: { borderBottomColor: '#334155' },
  menuIconContainer: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#f1f5f9', alignItems: 'center', justifyContent: 'center', marginRight: 12 },
  menuIconContainerDark: { backgroundColor: '#334155' },
  menuText: { flex: 1, fontSize: 16, fontWeight: '500', color: '#334155' },
  logoutText: { flex: 1, fontSize: 16, fontWeight: 'bold', color: '#e11d48' },
  sectionHeader: { paddingHorizontal: 16, paddingVertical: 12, backgroundColor: '#f8fafc', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  sectionHeaderDark: { backgroundColor: '#0f172a', borderBottomColor: '#334155' },
  sectionHeaderText: { fontSize: 13, fontWeight: 'bold', color: '#64748b', textTransform: 'uppercase', letterSpacing: 0.5 },
  sectionHeaderTextDark: { color: '#94a3b8' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContent: { backgroundColor: '#ffffff', width: '100%', borderRadius: 24, padding: 24, alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.2, shadowRadius: 20, elevation: 10 },
  modalContentDark: { backgroundColor: '#1e293b' },
  modalTitle: { fontSize: 22, fontWeight: 'bold', color: '#0f172a', marginBottom: 8 },
  modalMessage: { fontSize: 15, color: '#64748b', textAlign: 'center', marginBottom: 24, lineHeight: 22 },
  modalActions: { flexDirection: 'row', gap: 12, width: '100%' },
  modalButton: { flex: 1, paddingVertical: 14, borderRadius: 12, alignItems: 'center' },
  modalButtonCancel: { backgroundColor: '#f1f5f9' },
  modalButtonCancelDark: { backgroundColor: '#334155' },
  modalButtonConfirm: { backgroundColor: '#e11d48' },
  modalButtonTextCancel: { fontSize: 16, fontWeight: 'bold', color: '#475569' },
  modalButtonTextConfirm: { fontSize: 16, fontWeight: 'bold', color: '#ffffff' },
});
