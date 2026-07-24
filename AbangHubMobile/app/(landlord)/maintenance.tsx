import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, Image, TouchableOpacity, RefreshControl, Alert, Modal, TextInput } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import * as ImagePicker from 'expo-image-picker';

export default function LandlordMaintenanceScreen() {
  const { isDarkMode } = useTheme();
  const [requests, setRequests] = useState<any[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [selectedRequest, setSelectedRequest] = useState<any>(null);
  const [cost, setCost] = useState<string>('');
  const [statusModalVisible, setStatusModalVisible] = useState(false);
  const [receiptImage, setReceiptImage] = useState<string | null>(null);

  const pickImage = () => {
    Alert.alert(
      'Upload Receipt',
      'Choose an option',
      [
        {
          text: 'Take Photo',
          onPress: async () => {
            const { status } = await ImagePicker.requestCameraPermissionsAsync();
            if (status !== 'granted') {
              Alert.alert('Permission needed', 'Sorry, we need camera permissions to make this work!');
              return;
            }
            let result = await ImagePicker.launchCameraAsync({
              mediaTypes: ['images'],
              allowsEditing: true,
              quality: 0.8,
            });
            if (!result.canceled) {
              setReceiptImage(result.assets[0].uri);
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
              setReceiptImage(result.assets[0].uri);
            }
          }
        },
        { text: 'Cancel', style: 'cancel' }
      ]
    );
  };

  const fetchRequests = async () => {
    try {
      const response = await apiClient.get('/maintenance-requests');
      setRequests(response.data.data);
    } catch (error) {
      console.error('Error fetching maintenance requests', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchRequests();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchRequests();
  };

  const updateStatus = async (status: string) => {
    if (!selectedRequest) return;
    
    if (status === 'resolved' && !cost) {
      Alert.alert('Error', 'Please enter the repair cost.');
      return;
    }

    try {
      if (status === 'resolved' && receiptImage) {
        // Send as FormData
        const formData = new FormData();
        formData.append('status', status);
        formData.append('cost', cost);
        formData.append('_method', 'PUT'); // Laravel requires this for multipart PUT
        
        const filename = receiptImage.split('/').pop() || 'receipt.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image/jpeg`;
        
        formData.append('receipt_image', {
          uri: receiptImage,
          name: filename,
          type
        } as any);

        await apiClient.post(`/maintenance-requests/${selectedRequest.id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
      } else {
        // Send as JSON
        const payload: any = { status };
        if (status === 'resolved') {
          payload.cost = parseFloat(cost);
        }
        await apiClient.put(`/maintenance-requests/${selectedRequest.id}`, payload);
      }

      Alert.alert('Success', 'Status updated successfully.');
      setStatusModalVisible(false);
      setCost('');
      setReceiptImage(null);
      fetchRequests();
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to update status.');
    }
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'resolved': return '#22c55e'; // Green
      case 'in_progress': return '#3b82f6'; // Blue
      case 'pending': return '#f59e0b'; // Amber
      default: return '#64748b'; // Gray
    }
  };

  const getStatusLabel = (status: string) => {
    switch (status.toLowerCase()) {
      case 'in_progress': return 'IN PROGRESS';
      default: return status.toUpperCase();
    }
  };

  const renderRequestItem = ({ item }: { item: any }) => {
    const property = item.property;
    const tenant = item.tenant;
    
    // Resolve image URL
    let fullImageUrl = null;
    if (item.image_path) {
      fullImageUrl = item.image_path.startsWith('http') ? item.image_path : `${apiClient.defaults.baseURL?.replace('/api', '')}/storage/${item.image_path}`;
    }

    return (
      <View style={[styles.card, isDarkMode && styles.cardDark]}>
        <View style={styles.cardHeader}>
          <View style={styles.headerInfo}>
            <Text style={[styles.title, isDarkMode && styles.textDark]}>{item.title}</Text>
            <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
              <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>
                {getStatusLabel(item.status)}
              </Text>
            </View>
          </View>
        </View>

        <View style={styles.cardContent}>
          <Text style={[styles.description, isDarkMode && styles.textMuted]}>{item.description}</Text>
          
          {fullImageUrl && (
            <Image source={{ uri: fullImageUrl }} style={styles.issueImage} resizeMode="cover" />
          )}

          {property && (
            <View style={styles.infoRow}>
              <Ionicons name="home-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              <Text style={[styles.infoText, isDarkMode && styles.textMuted]} numberOfLines={1}>
                {property.title}
              </Text>
            </View>
          )}

          {tenant && (
            <View style={styles.infoRow}>
              <Ionicons name="person-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
                {tenant.full_name || tenant.username || 'Tenant'}
              </Text>
            </View>
          )}

          <View style={styles.infoRow}>
            <Ionicons name="alert-circle-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
              Urgency: <Text style={{ color: item.urgency === 'emergency' ? '#ef4444' : item.urgency === 'high' ? '#f97316' : isDarkMode ? '#f8fafc' : '#0f172a', fontWeight: '600' }}>
                {item.urgency ? item.urgency.toUpperCase() : 'NORMAL'}
              </Text>
            </Text>
          </View>

          {item.status === 'resolved' && item.cost && (
            <View style={styles.infoRow}>
              <Ionicons name="cash-outline" size={16} color="#22c55e" />
              <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
                Cost: <Text style={{ color: '#22c55e', fontWeight: '600' }}>₱{parseFloat(item.cost).toLocaleString()}</Text>
              </Text>
            </View>
          )}
          
          <View style={styles.infoRow}>
             <Ionicons name="time-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
             <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
                {new Date(item.created_at).toLocaleDateString()}
             </Text>
          </View>
        </View>

        <View style={[styles.cardFooter, isDarkMode && styles.cardFooterDark]}>
          <TouchableOpacity 
            style={[styles.actionButton, styles.updateButton]}
            onPress={() => {
              setSelectedRequest(item);
              setStatusModalVisible(true);
            }}
          >
            <Ionicons name="create-outline" size={18} color="#fff" />
            <Text style={styles.buttonText}>Update Status</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Maintenance</Text>
        <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Manage tenant repairs</Text>
      </View>

      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : requests.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Ionicons name="hammer-outline" size={80} color={isDarkMode ? '#334155' : '#cbd5e1'} />
          <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Requests Yet</Text>
          <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>
            Your tenants have not reported any issues.
          </Text>
        </View>
      ) : (
        <FlatList
          data={requests}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderRequestItem}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl 
              refreshing={refreshing} 
              onRefresh={handleRefresh} 
              colors={['#e11d48']}
              tintColor={isDarkMode ? '#e11d48' : '#e11d48'}
            />
          }
        />
      )}

      {/* Status Update Modal */}
      <Modal
        visible={statusModalVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setStatusModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.modalContentDark]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Update Status</Text>
              <TouchableOpacity onPress={() => { setStatusModalVisible(false); setCost(''); setReceiptImage(null); }}>
                <Ionicons name="close" size={24} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              </TouchableOpacity>
            </View>
            
            <View style={styles.modalBody}>
              <Text style={[styles.modalSubtitle, isDarkMode && styles.textMuted]}>Select new status for this request:</Text>
              
              <TouchableOpacity style={[styles.statusOption, { borderLeftColor: '#f59e0b' }]} onPress={() => updateStatus('pending')}>
                 <Text style={[styles.statusOptionText, isDarkMode && styles.textDark]}>Pending</Text>
              </TouchableOpacity>

              <TouchableOpacity style={[styles.statusOption, { borderLeftColor: '#3b82f6' }]} onPress={() => updateStatus('in_progress')}>
                <Text style={[styles.statusOptionText, isDarkMode && styles.textDark]}>In Progress</Text>
              </TouchableOpacity>
              
              <View style={[styles.statusOption, { borderLeftColor: '#22c55e', flexDirection: 'column', alignItems: 'flex-start', paddingVertical: 16 }]}>
                <Text style={[styles.statusOptionText, isDarkMode && styles.textDark, { marginBottom: 10 }]}>Resolved</Text>
                <View style={{ width: '100%' }}>
                  <Text style={[styles.inputLabel, isDarkMode && styles.textMuted]}>Total Repair Cost (₱)</Text>
                  <TextInput
                    style={[styles.input, isDarkMode && styles.inputDark]}
                    placeholder="Enter cost (e.g. 500)"
                    placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                    keyboardType="numeric"
                    value={cost}
                    onChangeText={setCost}
                  />

                  {/* Receipt Upload */}
                  <Text style={[styles.inputLabel, isDarkMode && styles.textMuted, { marginTop: 12 }]}>Upload Receipt / Photo (Optional)</Text>
                  <TouchableOpacity style={styles.uploadButton} onPress={pickImage}>
                    <Ionicons name="camera-outline" size={20} color="#e11d48" />
                    <Text style={styles.uploadButtonText}>{receiptImage ? 'Change Image' : 'Select Image'}</Text>
                  </TouchableOpacity>
                  
                  {receiptImage && (
                    <Image source={{ uri: receiptImage }} style={styles.receiptPreview} />
                  )}

                  <TouchableOpacity 
                    style={[styles.resolveButton, !cost && { opacity: 0.5 }, { marginTop: 16 }]} 
                    onPress={() => updateStatus('resolved')}
                    disabled={!cost}
                  >
                    <Text style={styles.resolveButtonText}>Mark as Resolved</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </View>
            
            <TouchableOpacity style={styles.cancelButton} onPress={() => { setStatusModalVisible(false); setCost(''); setReceiptImage(null); }}>
              <Text style={styles.cancelButtonText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  containerDark: {
    backgroundColor: '#0f172a',
  },
  header: {
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 15,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  headerDark: {
    backgroundColor: '#1e293b',
    borderBottomColor: '#334155',
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#0f172a',
    letterSpacing: -0.5,
  },
  modalSubtitle: {
    fontSize: 14,
    color: '#64748b',
    marginBottom: 16,
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '500',
    color: '#475569',
    marginBottom: 8,
  },
  input: {
    backgroundColor: '#f1f5f9',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    color: '#0f172a',
    marginBottom: 12,
  },
  inputDark: {
    backgroundColor: '#0f172a',
    borderColor: '#334155',
    color: '#f8fafc',
  },
  uploadButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(225, 29, 72, 0.1)',
    paddingVertical: 10,
    paddingHorizontal: 16,
    borderRadius: 8,
    alignSelf: 'flex-start',
    marginBottom: 8,
  },
  uploadButtonText: {
    color: '#e11d48',
    fontWeight: '600',
    fontSize: 14,
    marginLeft: 8,
  },
  receiptPreview: {
    width: '100%',
    height: 120,
    borderRadius: 8,
    marginBottom: 8,
    backgroundColor: '#f1f5f9',
  },
  resolveButton: {
    backgroundColor: '#22c55e',
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  resolveButtonText: {
    color: '#ffffff',
    fontWeight: '600',
    fontSize: 14,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#64748b',
    marginTop: 4,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#334155',
    marginTop: 16,
  },
  emptySubtitle: {
    fontSize: 15,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 8,
  },
  listContainer: {
    padding: 16,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    marginBottom: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  cardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  cardHeader: {
    padding: 16,
    paddingBottom: 8,
  },
  headerInfo: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  title: {
    fontSize: 18,
    fontWeight: '700',
    color: '#0f172a',
    flex: 1,
    marginRight: 12,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '700',
  },
  cardContent: {
    padding: 16,
    paddingTop: 4,
  },
  description: {
    fontSize: 14,
    color: '#475569',
    lineHeight: 20,
    marginBottom: 12,
  },
  issueImage: {
    width: '100%',
    height: 150,
    borderRadius: 8,
    marginBottom: 12,
    backgroundColor: '#f1f5f9',
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 6,
  },
  infoText: {
    fontSize: 14,
    color: '#475569',
    marginLeft: 8,
    flex: 1,
  },
  textDark: {
    color: '#f8fafc',
  },
  textMuted: {
    color: '#94a3b8',
  },
  cardFooter: {
    flexDirection: 'row',
    padding: 12,
    backgroundColor: '#f8fafc',
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
  },
  cardFooterDark: {
    backgroundColor: '#0f172a',
    borderTopColor: '#334155',
  },
  actionButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
  },
  updateButton: {
    backgroundColor: '#e11d48',
  },
  buttonText: {
    color: '#ffffff',
    fontWeight: '600',
    fontSize: 14,
    marginLeft: 6,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  modalContent: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    width: '100%',
    maxWidth: 400,
    padding: 20,
  },
  modalContentDark: {
    backgroundColor: '#1e293b',
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 20,
    textAlign: 'center',
  },
  statusOption: {
    paddingVertical: 15,
    paddingHorizontal: 20,
    borderLeftWidth: 4,
    backgroundColor: '#f8fafc',
    marginBottom: 10,
    borderRadius: 8,
  },
  statusOptionText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#334155',
  },
  cancelButton: {
    marginTop: 10,
    paddingVertical: 15,
    alignItems: 'center',
  },
  cancelButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#64748b',
  },
});
