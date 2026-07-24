import React, { useState, useCallback, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, Modal, TextInput, Alert, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function TenantMaintenanceScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [requests, setRequests] = useState<any[]>([]);
  
  // Property ID needed to submit a request (we can fetch it from the dashboard if they have an active lease)
  const [propertyId, setPropertyId] = useState<number | null>(null);

  // Modal State
  const [modalVisible, setModalVisible] = useState(false);
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const fetchData = async () => {
    try {
      // Fetch maintenance requests
      const response = await apiClient.get('/maintenance-requests');
      setRequests(response.data.data);
      
      // Also fetch dashboard to get active property ID
      const dashResponse = await apiClient.get('/tenant/dashboard');
      if (dashResponse.data.data?.active_lease) {
        setPropertyId(dashResponse.data.data.active_lease.property_id);
      }
    } catch (error) {
      console.error('Error fetching maintenance requests', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchData();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchData();
  };

  const pickImage = () => {
    Alert.alert(
      'Attach Photo',
      'Choose an option',
      [
        {
          text: 'Take Photo',
          onPress: async () => {
            const { status } = await ImagePicker.requestCameraPermissionsAsync();
            if (status !== 'granted') {
              Alert.alert('Permission needed', 'Sorry, we need camera permissions!');
              return;
            }
            let result = await ImagePicker.launchCameraAsync({
              mediaTypes: ['images'],
              allowsEditing: true,
              quality: 0.8,
            });
            if (!result.canceled) {
              setImageUri(result.assets[0].uri);
            }
          }
        },
        {
          text: 'Choose from Gallery',
          onPress: async () => {
            let result = await ImagePicker.launchImageLibraryAsync({
              mediaTypes: ['images'],
              allowsEditing: true,
              quality: 0.8,
            });
            if (!result.canceled) {
              setImageUri(result.assets[0].uri);
            }
          }
        },
        { text: 'Cancel', style: 'cancel' }
      ]
    );
  };

  const submitRequest = async () => {
    if (!title || !description) {
      Alert.alert('Error', 'Please enter a title and description.');
      return;
    }
    if (!propertyId) {
      Alert.alert('Error', 'No active lease found. You cannot submit a request.');
      return;
    }

    try {
      setSubmitting(true);
      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('property_id', propertyId.toString());

      if (imageUri) {
        const filename = imageUri.split('/').pop() || 'damage.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image/jpeg`;
        
        formData.append('image', {
          uri: imageUri,
          name: filename,
          type
        } as any);
      }

      await apiClient.post('/maintenance-requests', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      Alert.alert('Success', 'Maintenance request submitted.');
      setModalVisible(false);
      setTitle('');
      setDescription('');
      setImageUri(null);
      fetchData();
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to submit request.');
    } finally {
      setSubmitting(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'resolved': return '#10b981';
      case 'in_progress': return '#3b82f6';
      default: return '#f59e0b';
    }
  };

  const renderItem = ({ item }: { item: any }) => {
    let fullImageUrl = null;
    if (item.image_path) {
      fullImageUrl = item.image_path.startsWith('http') ? item.image_path : `${apiClient.defaults.baseURL?.replace('/api', '')}/storage/${item.image_path}`;
    }

    return (
      <View style={[styles.card, isDarkMode && styles.cardDark]}>
        <View style={styles.cardHeader}>
          <Text style={[styles.title, isDarkMode && styles.textDark]}>{item.title}</Text>
          <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
            <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>{item.status.toUpperCase()}</Text>
          </View>
        </View>
        <Text style={[styles.description, isDarkMode && styles.textMuted]}>{item.description}</Text>
        
        {fullImageUrl && (
          <Image source={{ uri: fullImageUrl }} style={styles.attachedImage} />
        )}
        
        <View style={styles.footer}>
          <Text style={[styles.date, isDarkMode && styles.textMuted]}>{item.created_at}</Text>
        </View>
      </View>
    );
  };

  if (loading && requests.length === 0) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Maintenance</Text>
      </View>

      <FlatList
        data={requests}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={styles.listContent}
        refreshing={refreshing}
        onRefresh={handleRefresh}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="hammer-outline" size={60} color={isDarkMode ? '#334155' : '#cbd5e1'} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Requests Yet</Text>
            <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>You haven't reported any issues.</Text>
          </View>
        }
      />

      {propertyId && (
        <TouchableOpacity style={styles.fab} onPress={() => setModalVisible(true)}>
          <Ionicons name="add" size={30} color="#fff" />
        </TouchableOpacity>
      )}

      {/* Add Request Modal */}
      <Modal visible={modalVisible} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.cardDark]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Report Issue</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={24} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              </TouchableOpacity>
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Issue Title</Text>
              <TextInput
                style={[styles.input, isDarkMode && styles.inputDark]}
                placeholder="e.g. Leaking Faucet"
                placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                value={title}
                onChangeText={setTitle}
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Description</Text>
              <TextInput
                style={[styles.input, styles.textArea, isDarkMode && styles.inputDark]}
                placeholder="Describe the problem..."
                placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                value={description}
                onChangeText={setDescription}
                multiline
                numberOfLines={4}
                textAlignVertical="top"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Photo (Optional)</Text>
              {imageUri ? (
                <View style={styles.imagePreviewContainer}>
                  <Image source={{ uri: imageUri }} style={styles.imagePreview} />
                  <TouchableOpacity style={styles.removeImageBtn} onPress={() => setImageUri(null)}>
                    <Ionicons name="trash" size={20} color="#fff" />
                  </TouchableOpacity>
                </View>
              ) : (
                <TouchableOpacity style={[styles.uploadBtn, isDarkMode && styles.uploadBtnDark]} onPress={pickImage}>
                  <Ionicons name="camera-outline" size={24} color="#e11d48" />
                  <Text style={styles.uploadBtnText}>Take or Choose Photo</Text>
                </TouchableOpacity>
              )}
            </View>

            <TouchableOpacity 
              style={[styles.submitBtn, submitting && styles.submitBtnDisabled]} 
              onPress={submitRequest}
              disabled={submitting}
            >
              {submitting ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.submitBtnText}>Submit Request</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: 20, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  headerTitle: { fontSize: 24, fontWeight: '700', color: '#0f172a' },
  listContent: { padding: 16, paddingBottom: 100 },
  card: { backgroundColor: '#fff', borderRadius: 16, padding: 16, marginBottom: 16, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 6 },
  cardDark: { backgroundColor: '#1e293b' },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 },
  title: { fontSize: 16, fontWeight: '700', color: '#0f172a', flex: 1 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12 },
  statusText: { fontSize: 11, fontWeight: 'bold' },
  description: { fontSize: 14, color: '#475569', lineHeight: 20, marginBottom: 12 },
  attachedImage: { width: '100%', height: 180, borderRadius: 12, marginBottom: 12 },
  footer: { flexDirection: 'row', justifyContent: 'flex-end', borderTopWidth: 1, borderTopColor: '#f1f5f9', paddingTop: 12 },
  date: { fontSize: 12, color: '#94a3b8' },
  fab: { position: 'absolute', bottom: 24, right: 24, backgroundColor: '#e11d48', width: 60, height: 60, borderRadius: 30, justifyContent: 'center', alignItems: 'center', elevation: 5, shadowColor: '#e11d48', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 8 },
  emptyContainer: { alignItems: 'center', marginTop: 60 },
  emptyTitle: { fontSize: 18, fontWeight: '600', color: '#0f172a', marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: '#64748b', marginTop: 8 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalContent: { backgroundColor: '#fff', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, minHeight: '60%' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 },
  modalTitle: { fontSize: 20, fontWeight: '700', color: '#0f172a' },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 14, fontWeight: '500', color: '#475569', marginBottom: 8 },
  input: { backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 12, padding: 12, fontSize: 15, color: '#0f172a' },
  inputDark: { backgroundColor: '#0f172a', borderColor: '#334155', color: '#f8fafc' },
  textArea: { height: 100 },
  uploadBtn: { backgroundColor: '#fff1f2', borderWidth: 1, borderColor: '#ffe4e6', borderStyle: 'dashed', borderRadius: 12, padding: 24, alignItems: 'center' },
  uploadBtnDark: { backgroundColor: '#4c1d9520', borderColor: '#4c1d95' },
  uploadBtnText: { color: '#e11d48', fontWeight: '600', marginTop: 8 },
  imagePreviewContainer: { position: 'relative', width: '100%', height: 150, borderRadius: 12, overflow: 'hidden' },
  imagePreview: { width: '100%', height: '100%' },
  removeImageBtn: { position: 'absolute', top: 8, right: 8, backgroundColor: 'rgba(0,0,0,0.5)', padding: 8, borderRadius: 20 },
  submitBtn: { backgroundColor: '#e11d48', padding: 16, borderRadius: 12, alignItems: 'center', marginTop: 24 },
  submitBtnDisabled: { opacity: 0.7 },
  submitBtnText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
});
