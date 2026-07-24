import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import { LinearGradient } from 'expo-linear-gradient';

export default function TenantWalletScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [balance, setBalance] = useState(0);
  const [transactions, setTransactions] = useState<any[]>([]);

  const fetchData = async () => {
    try {
      const [balRes, txRes] = await Promise.all([
        apiClient.get('/wallet'),
        apiClient.get('/transactions')
      ]);
      setBalance(balRes.data.balance);
      setTransactions(txRes.data.data || []);
    } catch (error) {
      console.error('Error fetching wallet data', error);
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

  const renderTransaction = ({ item }: { item: any }) => (
    <View style={[styles.txCard, isDarkMode && styles.cardDark]}>
      <View style={styles.txIconContainer}>
        <Ionicons 
          name={item.type === 'credit' ? 'arrow-down' : 'arrow-up'} 
          size={20} 
          color={item.type === 'credit' ? '#10b981' : '#e11d48'} 
        />
      </View>
      <View style={styles.txDetails}>
        <Text style={[styles.txDescription, isDarkMode && styles.textDark]}>{item.description}</Text>
        <Text style={[styles.txDate, isDarkMode && styles.textMuted]}>{item.created_at}</Text>
      </View>
      <Text style={[styles.txAmount, { color: item.type === 'credit' ? '#10b981' : '#e11d48' }]}>
        {item.type === 'credit' ? '+' : '-'}₱{parseFloat(item.amount).toLocaleString()}
      </Text>
    </View>
  );

  if (loading && transactions.length === 0) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Wallet</Text>
      </View>

      <FlatList
        data={transactions}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderTransaction}
        contentContainerStyle={styles.listContent}
        refreshing={refreshing}
        onRefresh={handleRefresh}
        ListHeaderComponent={
          <LinearGradient
            colors={isDarkMode ? ['#1e293b', '#0f172a'] : ['#e11d48', '#be123c']}
            style={styles.balanceCard}
          >
            <Text style={styles.balanceLabel}>Available Balance</Text>
            <Text style={styles.balanceAmount}>₱{balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</Text>
          </LinearGradient>
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="wallet-outline" size={60} color={isDarkMode ? '#334155' : '#cbd5e1'} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Transactions Yet</Text>
            <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>Your transaction history will appear here.</Text>
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
  balanceCard: { padding: 24, borderRadius: 16, marginBottom: 24, alignItems: 'center', shadowColor: '#e11d48', shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.3, shadowRadius: 16, elevation: 8 },
  balanceLabel: { color: 'rgba(255,255,255,0.8)', fontSize: 14, textTransform: 'uppercase', letterSpacing: 1, marginBottom: 8 },
  balanceAmount: { color: '#fff', fontSize: 36, fontWeight: 'bold' },
  txCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, flexDirection: 'row', alignItems: 'center', elevation: 1, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 3 },
  cardDark: { backgroundColor: '#1e293b' },
  txIconContainer: { width: 40, height: 40, borderRadius: 20, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center', marginRight: 12 },
  txDetails: { flex: 1 },
  txDescription: { fontSize: 15, fontWeight: '600', color: '#0f172a', marginBottom: 4 },
  txDate: { fontSize: 12, color: '#64748b' },
  txAmount: { fontSize: 16, fontWeight: '700' },
  emptyContainer: { alignItems: 'center', marginTop: 40 },
  emptyTitle: { fontSize: 18, fontWeight: '600', color: '#0f172a', marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: '#64748b', marginTop: 8 },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
});
