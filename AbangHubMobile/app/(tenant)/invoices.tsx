import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, TouchableOpacity, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function TenantInvoicesScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [invoices, setInvoices] = useState<any[]>([]);

  const fetchInvoices = async () => {
    try {
      const response = await apiClient.get('/invoices');
      setInvoices(response.data.data);
    } catch (error) {
      console.error('Error fetching invoices', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchInvoices();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchInvoices();
  };

  const uploadReceipt = (invoiceId: number) => {
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
              submitReceipt(invoiceId, result.assets[0].uri);
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
              submitReceipt(invoiceId, result.assets[0].uri);
            }
          }
        },
        { text: 'Cancel', style: 'cancel' }
      ]
    );
  };

  const submitReceipt = async (invoiceId: number, uri: string) => {
    try {
      setLoading(true);
      const formData = new FormData();
      formData.append('status', 'paid');
      formData.append('_method', 'PUT'); // Laravel requirement for multipart PUT
      
      const filename = uri.split('/').pop() || 'receipt.jpg';
      const match = /\.(\w+)$/.exec(filename);
      const type = match ? `image/${match[1]}` : `image/jpeg`;
      
      formData.append('receipt_image', {
        uri,
        name: filename,
        type
      } as any);

      await apiClient.post(`/invoices/${invoiceId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      Alert.alert('Success', 'Proof of payment submitted!');
      fetchInvoices();
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to submit receipt.');
      setLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'paid': return '#10b981';
      case 'overdue': return '#ef4444';
      default: return '#f59e0b';
    }
  };

  const renderItem = ({ item }: { item: any }) => (
    <View style={[styles.card, isDarkMode && styles.cardDark]}>
      <View style={styles.cardHeader}>
        <Text style={[styles.title, isDarkMode && styles.textDark]}>{item.description}</Text>
        <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
          <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>{item.status.toUpperCase()}</Text>
        </View>
      </View>
      
      <View style={styles.detailsRow}>
        <View>
          <Text style={[styles.label, isDarkMode && styles.textMuted]}>Amount Due</Text>
          <Text style={[styles.amount, isDarkMode && styles.textDark]}>₱{parseFloat(item.amount).toLocaleString()}</Text>
        </View>
        <View style={styles.alignRight}>
          <Text style={[styles.label, isDarkMode && styles.textMuted]}>Due Date</Text>
          <Text style={[styles.value, isDarkMode && styles.textDark]}>{item.due_date}</Text>
        </View>
      </View>

      {item.status !== 'paid' && (
        <TouchableOpacity style={styles.uploadBtn} onPress={() => uploadReceipt(item.id)}>
          <Ionicons name="camera-outline" size={20} color="#fff" />
          <Text style={styles.uploadBtnText}>Upload Proof of Payment</Text>
        </TouchableOpacity>
      )}
    </View>
  );

  if (loading && invoices.length === 0) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Invoices & Payments</Text>
      </View>

      <FlatList
        data={invoices}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={styles.listContent}
        refreshing={refreshing}
        onRefresh={handleRefresh}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="receipt-outline" size={60} color={isDarkMode ? '#334155' : '#cbd5e1'} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Invoices</Text>
            <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>You don't have any rent invoices yet.</Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: 20, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  headerTitle: { fontSize: 24, fontWeight: '700', color: '#0f172a' },
  listContent: { padding: 16, paddingBottom: 40 },
  card: { backgroundColor: '#fff', borderRadius: 16, padding: 16, marginBottom: 16, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 6 },
  cardDark: { backgroundColor: '#1e293b' },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  title: { fontSize: 16, fontWeight: '600', color: '#0f172a', flex: 1 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12 },
  statusText: { fontSize: 12, fontWeight: 'bold' },
  detailsRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  alignRight: { alignItems: 'flex-end' },
  label: { fontSize: 13, color: '#64748b', marginBottom: 4 },
  amount: { fontSize: 18, fontWeight: '700', color: '#e11d48' },
  value: { fontSize: 15, fontWeight: '500', color: '#0f172a' },
  uploadBtn: { backgroundColor: '#e11d48', flexDirection: 'row', alignItems: 'center', justifyContent: 'center', padding: 12, borderRadius: 12 },
  uploadBtnText: { color: '#fff', fontWeight: 'bold', marginLeft: 8 },
  emptyContainer: { alignItems: 'center', marginTop: 60 },
  emptyTitle: { fontSize: 18, fontWeight: '600', color: '#0f172a', marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: '#64748b', marginTop: 8 },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
});
