import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, TouchableOpacity, RefreshControl, Alert, Modal, TextInput } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import { Picker } from '@react-native-picker/picker';

export default function LandlordInvoicesScreen() {
  const { isDarkMode } = useTheme();
  const [invoices, setInvoices] = useState<any[]>([]);
  const [leases, setLeases] = useState<any[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  
  // Modal states
  const [addModalVisible, setAddModalVisible] = useState(false);
  const [selectedLeaseId, setSelectedLeaseId] = useState<string>('');
  const [amount, setAmount] = useState<string>('');
  const [description, setDescription] = useState<string>('Monthly Rent');
  
  // Hardcoded due date for simplicity (1 week from now)
  const getDueDate = () => {
    const date = new Date();
    date.setDate(date.getDate() + 7);
    return date.toISOString().split('T')[0];
  };

  const fetchData = async () => {
    try {
      const [invoicesRes, leasesRes] = await Promise.all([
        apiClient.get('/landlord/invoices'),
        apiClient.get('/landlord/leases')
      ]);
      setInvoices(invoicesRes.data.data);
      setLeases(leasesRes.data.data);
      if (leasesRes.data.data.length > 0) {
        setSelectedLeaseId(leasesRes.data.data[0].id.toString());
      }
    } catch (error) {
      console.error('Error fetching invoices/leases', error);
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

  const handleCreateInvoice = async () => {
    if (!amount || isNaN(Number(amount))) {
      Alert.alert('Error', 'Please enter a valid amount');
      return;
    }

    try {
      await apiClient.post('/landlord/invoices', {
        lease_id: selectedLeaseId,
        amount: parseFloat(amount),
        description,
        due_date: getDueDate()
      });
      Alert.alert('Success', 'Invoice created successfully.');
      setAddModalVisible(false);
      setAmount('');
      fetchData();
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to create invoice');
    }
  };

  const markAsPaid = async (id: number) => {
    Alert.alert(
      'Mark as Paid',
      'Are you sure you want to mark this invoice as paid?',
      [
        { text: 'Cancel', style: 'cancel' },
        { 
          text: 'Confirm', 
          onPress: async () => {
            try {
              await apiClient.put(`/landlord/invoices/${id}/status`, { status: 'paid' });
              fetchData();
            } catch (error) {
              Alert.alert('Error', 'Failed to update status.');
            }
          }
        }
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'paid': return '#22c55e';
      case 'overdue': return '#e11d48';
      default: return '#f59e0b';
    }
  };

  const renderInvoiceItem = ({ item }: { item: any }) => {
    const tenant = item.lease?.tenant;
    const property = item.lease?.property;

    return (
      <View style={[styles.card, isDarkMode && styles.cardDark]}>
        <View style={styles.cardHeader}>
          <Text style={[styles.description, isDarkMode && styles.textDark]}>{item.description}</Text>
          <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
            <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>
              {item.status.toUpperCase()}
            </Text>
          </View>
        </View>

        <View style={styles.cardContent}>
          <Text style={[styles.amount, isDarkMode && styles.textDark]}>
            ₱{parseFloat(item.amount).toLocaleString()}
          </Text>
          
          <View style={styles.infoRow}>
            <Ionicons name="person-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
              {tenant?.full_name || tenant?.username || 'Unknown'}
            </Text>
          </View>
          
          <View style={styles.infoRow}>
            <Ionicons name="home-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>{property?.title}</Text>
          </View>
          
          <View style={styles.infoRow}>
            <Ionicons name="calendar-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
              Due: {new Date(item.due_date).toLocaleDateString()}
            </Text>
          </View>
        </View>

        {item.status !== 'paid' && (
          <TouchableOpacity style={styles.payButton} onPress={() => markAsPaid(item.id)}>
            <Ionicons name="checkmark-circle-outline" size={20} color="#fff" />
            <Text style={styles.payButtonText}>Mark as Paid</Text>
          </TouchableOpacity>
        )}
      </View>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Invoices</Text>
        <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Track rent and bills</Text>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : invoices.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Ionicons name="receipt-outline" size={80} color={isDarkMode ? '#334155' : '#cbd5e1'} />
          <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Invoices Yet</Text>
          <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>
            Create your first invoice to start collecting rent.
          </Text>
        </View>
      ) : (
        <FlatList
          data={invoices}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderInvoiceItem}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" colors={['#e11d48']} />
          }
        />
      )}

      {/* FAB to Add Invoice */}
      <TouchableOpacity style={styles.fab} onPress={() => setAddModalVisible(true)}>
        <Ionicons name="add" size={32} color="#fff" />
      </TouchableOpacity>

      {/* Add Invoice Modal */}
      <Modal visible={addModalVisible} transparent={true} animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.modalContentDark]}>
            <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Create New Invoice</Text>
            
            {leases.length === 0 ? (
              <Text style={styles.errorText}>You have no active tenants to bill.</Text>
            ) : (
              <>
                <Text style={[styles.label, isDarkMode && styles.textDark]}>Select Tenant / Property</Text>
                <View style={[styles.pickerContainer, isDarkMode && styles.pickerContainerDark]}>
                  <Picker
                    selectedValue={selectedLeaseId}
                    onValueChange={(itemValue) => setSelectedLeaseId(itemValue)}
                    style={[styles.picker, isDarkMode && styles.textDark]}
                    dropdownIconColor={isDarkMode ? "#f8fafc" : "#0f172a"}
                  >
                    {leases.map((lease) => (
                      <Picker.Item 
                        key={lease.id} 
                        label={`${lease.tenant?.full_name || 'Tenant'} - ${lease.property?.title}`} 
                        value={lease.id.toString()} 
                      />
                    ))}
                  </Picker>
                </View>

                <Text style={[styles.label, isDarkMode && styles.textDark]}>Amount (₱)</Text>
                <TextInput
                  style={[styles.input, isDarkMode && styles.inputDark]}
                  placeholder="e.g. 15000"
                  placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                  keyboardType="numeric"
                  value={amount}
                  onChangeText={setAmount}
                />

                <Text style={[styles.label, isDarkMode && styles.textDark]}>Description</Text>
                <TextInput
                  style={[styles.input, isDarkMode && styles.inputDark]}
                  placeholder="e.g. Monthly Rent"
                  placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                  value={description}
                  onChangeText={setDescription}
                />

                <TouchableOpacity style={styles.submitButton} onPress={handleCreateInvoice}>
                  <Text style={styles.submitButtonText}>Create Bill</Text>
                </TouchableOpacity>
              </>
            )}

            <TouchableOpacity style={styles.cancelButton} onPress={() => setAddModalVisible(false)}>
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
  headerSubtitle: {
    fontSize: 16,
    color: '#64748b',
    marginTop: 4,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 30,
  },
  emptyTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#334155',
    marginTop: 16,
  },
  emptySubtitle: {
    fontSize: 15,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 22,
  },
  listContainer: {
    padding: 16,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
    overflow: 'hidden',
  },
  cardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    paddingBottom: 8,
  },
  description: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
    flex: 1,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 11,
    fontWeight: '700',
  },
  cardContent: {
    padding: 16,
    paddingTop: 0,
  },
  amount: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#e11d48',
    marginBottom: 12,
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
  payButton: {
    flexDirection: 'row',
    backgroundColor: '#10b981',
    padding: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },
  payButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    marginLeft: 8,
    fontSize: 16,
  },
  textDark: {
    color: '#f8fafc',
  },
  textMuted: {
    color: '#94a3b8',
  },
  fab: {
    position: 'absolute',
    bottom: 24,
    right: 24,
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#e11d48',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 24,
    minHeight: 400,
  },
  modalContentDark: {
    backgroundColor: '#1e293b',
  },
  modalTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 20,
    textAlign: 'center',
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#334155',
    marginBottom: 8,
    marginTop: 12,
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    backgroundColor: '#f8fafc',
    marginBottom: 10,
    overflow: 'hidden',
  },
  pickerContainerDark: {
    borderColor: '#334155',
    backgroundColor: '#0f172a',
  },
  picker: {
    height: 50,
    width: '100%',
  },
  input: {
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    color: '#0f172a',
    backgroundColor: '#f8fafc',
  },
  inputDark: {
    borderColor: '#334155',
    backgroundColor: '#0f172a',
    color: '#f8fafc',
  },
  submitButton: {
    backgroundColor: '#e11d48',
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 24,
  },
  submitButtonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '700',
  },
  cancelButton: {
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: 8,
  },
  cancelButtonText: {
    color: '#64748b',
    fontSize: 16,
    fontWeight: '600',
  },
  errorText: {
    color: '#e11d48',
    textAlign: 'center',
    fontSize: 16,
    marginVertical: 20,
  },
});
