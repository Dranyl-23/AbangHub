import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, Modal, TextInput, Alert, Image } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import * as ImagePicker from 'expo-image-picker';

export default function ExpensesScreen() {
  const { isDarkMode } = useTheme();
  const [expenses, setExpenses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Add Expense Modal State
  const [modalVisible, setModalVisible] = useState(false);
  const [amount, setAmount] = useState('');
  const [category, setCategory] = useState('');
  const [description, setDescription] = useState('');
  const [date, setDate] = useState(new Date().toISOString().split('T')[0]);
  const [receiptImage, setReceiptImage] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const fetchExpenses = async () => {
    try {
      const response = await apiClient.get('/expenses');
      setExpenses(response.data.data);
    } catch (error) {
      console.error('Error fetching expenses:', error);
    } finally {
      setLoading(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchExpenses();
    }, [])
  );

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

  const handleAddExpense = async () => {
    if (!amount || !category || !date) {
      Alert.alert('Error', 'Amount, category, and date are required.');
      return;
    }

    setSubmitting(true);
    try {
      if (receiptImage) {
        const formData = new FormData();
        formData.append('amount', amount);
        formData.append('category', category);
        formData.append('description', description);
        formData.append('date', date);
        
        const filename = receiptImage.split('/').pop() || 'receipt.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image/jpeg`;
        
        formData.append('receipt_image', {
          uri: receiptImage,
          name: filename,
          type
        } as any);

        await apiClient.post('/expenses', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        await apiClient.post('/expenses', {
          amount, category, description, date
        });
      }

      Alert.alert('Success', 'Expense logged successfully!');
      setModalVisible(false);
      resetForm();
      fetchExpenses();
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to add expense.');
    } finally {
      setSubmitting(false);
    }
  };

  const resetForm = () => {
    setAmount('');
    setCategory('');
    setDescription('');
    setDate(new Date().toISOString().split('T')[0]);
    setReceiptImage(null);
  };

  const renderExpenseItem = ({ item }: { item: any }) => (
    <View style={[styles.expenseCard, isDarkMode && styles.cardDark]}>
      <View style={styles.expenseHeader}>
        <View style={styles.categoryBadge}>
          <Text style={styles.categoryText}>{item.category}</Text>
        </View>
        <Text style={[styles.amountText, isDarkMode && styles.textDark]}>₱{parseFloat(item.amount).toLocaleString()}</Text>
      </View>
      {item.description ? (
        <Text style={[styles.descriptionText, isDarkMode && styles.textMuted]}>{item.description}</Text>
      ) : null}
      <View style={styles.expenseFooter}>
        <Text style={[styles.dateText, isDarkMode && styles.textMuted]}>
          <Ionicons name="calendar-outline" size={14} /> {item.date}
        </Text>
        {item.receipt_image_path && (
          <Ionicons name="document-text-outline" size={16} color="#64748b" />
        )}
      </View>
    </View>
  );

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Expense Tracker</Text>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : (
        <FlatList
          data={expenses}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderExpenseItem}
          contentContainerStyle={styles.listContent}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="receipt-outline" size={64} color={isDarkMode ? '#334155' : '#cbd5e1'} />
              <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Expenses Yet</Text>
              <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>Tap the + button to log an expense.</Text>
            </View>
          }
        />
      )}

      {/* Floating Action Button */}
      <TouchableOpacity style={styles.fab} onPress={() => setModalVisible(true)}>
        <Ionicons name="add" size={30} color="#fff" />
      </TouchableOpacity>

      {/* Add Expense Modal */}
      <Modal visible={modalVisible} animationType="slide" transparent={true}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.modalContentDark]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Log Expense</Text>
              <TouchableOpacity onPress={() => { setModalVisible(false); resetForm(); }}>
                <Ionicons name="close" size={24} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              </TouchableOpacity>
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Amount (₱)</Text>
              <TextInput style={[styles.input, isDarkMode && styles.inputDark]} keyboardType="numeric" value={amount} onChangeText={setAmount} placeholder="e.g. 1500" placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'} />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Category</Text>
              <TextInput style={[styles.input, isDarkMode && styles.inputDark]} value={category} onChangeText={setCategory} placeholder="e.g. Taxes, Maintenance" placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'} />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Date (YYYY-MM-DD)</Text>
              <TextInput style={[styles.input, isDarkMode && styles.inputDark]} value={date} onChangeText={setDate} />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Description (Optional)</Text>
              <TextInput style={[styles.input, isDarkMode && styles.inputDark]} value={description} onChangeText={setDescription} placeholder="Brief details" placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'} />
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Receipt Photo (Optional)</Text>
              <TouchableOpacity style={styles.uploadBtn} onPress={pickImage}>
                <Ionicons name="camera" size={20} color="#e11d48" />
                <Text style={styles.uploadBtnText}>{receiptImage ? 'Change Photo' : 'Select Photo'}</Text>
              </TouchableOpacity>
              {receiptImage && <Image source={{ uri: receiptImage }} style={styles.previewImage} />}
            </View>

            <TouchableOpacity style={[styles.saveBtn, submitting && { opacity: 0.7 }]} onPress={handleAddExpense} disabled={submitting}>
              {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.saveBtnText}>Save Expense</Text>}
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
  header: { flexDirection: 'row', alignItems: 'center', padding: 20, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  backButton: { marginRight: 16 },
  headerTitle: { fontSize: 20, fontWeight: '700', color: '#0f172a' },
  centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  listContent: { padding: 16, paddingBottom: 100 },
  expenseCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 6, elevation: 2 },
  cardDark: { backgroundColor: '#1e293b' },
  expenseHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  categoryBadge: { backgroundColor: 'rgba(225, 29, 72, 0.1)', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 12 },
  categoryText: { color: '#e11d48', fontSize: 12, fontWeight: '600' },
  amountText: { fontSize: 18, fontWeight: '700', color: '#0f172a' },
  descriptionText: { fontSize: 14, color: '#475569', marginBottom: 12 },
  expenseFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  dateText: { fontSize: 13, color: '#64748b' },
  fab: { position: 'absolute', bottom: 24, right: 24, backgroundColor: '#e11d48', width: 60, height: 60, borderRadius: 30, justifyContent: 'center', alignItems: 'center', shadowColor: '#e11d48', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 8, elevation: 5 },
  emptyContainer: { alignItems: 'center', marginTop: 100 },
  emptyTitle: { fontSize: 18, fontWeight: '600', marginTop: 16, color: '#0f172a' },
  emptySubtitle: { fontSize: 14, color: '#64748b', marginTop: 8 },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalContent: { backgroundColor: '#fff', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, maxHeight: '90%' },
  modalContentDark: { backgroundColor: '#1e293b' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 },
  modalTitle: { fontSize: 20, fontWeight: '700', color: '#0f172a' },
  inputGroup: { marginBottom: 16 },
  label: { fontSize: 14, fontWeight: '600', color: '#475569', marginBottom: 8 },
  input: { backgroundColor: '#f1f5f9', borderRadius: 12, padding: 14, fontSize: 16, color: '#0f172a' },
  inputDark: { backgroundColor: '#0f172a', color: '#f8fafc' },
  uploadBtn: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(225, 29, 72, 0.1)', padding: 12, borderRadius: 12, alignSelf: 'flex-start' },
  uploadBtnText: { color: '#e11d48', fontWeight: '600', marginLeft: 8 },
  previewImage: { width: '100%', height: 120, borderRadius: 12, marginTop: 12 },
  saveBtn: { backgroundColor: '#e11d48', padding: 16, borderRadius: 12, alignItems: 'center', marginTop: 8 },
  saveBtnText: { color: '#fff', fontSize: 16, fontWeight: '700' },
});
