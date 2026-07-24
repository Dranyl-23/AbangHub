import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, Image, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function LandlordTenantsScreen() {
  const { isDarkMode } = useTheme();
  const [leases, setLeases] = useState<any[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchLeases = async () => {
    try {
      const response = await apiClient.get('/landlord/leases');
      setLeases(response.data.data);
    } catch (error) {
      console.error('Error fetching leases', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchLeases();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchLeases();
  };

  const renderLeaseItem = ({ item }: { item: any }) => {
    const tenant = item.tenant;
    const property = item.property;

    return (
      <View style={[styles.card, isDarkMode && styles.cardDark]}>
        <View style={styles.cardHeader}>
          <View style={styles.tenantInfo}>
            <View style={styles.avatarContainer}>
              {tenant?.avatar ? (
                <Image source={{ uri: tenant.avatar }} style={styles.avatar} />
              ) : (
                <Ionicons name="person" size={24} color="#94a3b8" />
              )}
            </View>
            <View style={styles.tenantText}>
              <Text style={[styles.tenantName, isDarkMode && styles.textDark]}>
                {tenant?.full_name || tenant?.username || 'Unknown Tenant'}
              </Text>
              <Text style={styles.tenantEmail}>{tenant?.email}</Text>
            </View>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: item.status === 'active' ? '#22c55e20' : '#64748b20' }]}>
            <Text style={[styles.statusText, { color: item.status === 'active' ? '#22c55e' : '#64748b' }]}>
              {item.status.toUpperCase()}
            </Text>
          </View>
        </View>

        <View style={styles.cardContent}>
          <View style={styles.infoRow}>
            <Ionicons name="home-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>{property?.title || 'Unknown Property'}</Text>
          </View>
          
          <View style={styles.infoRow}>
            <Ionicons name="cash-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
              ₱{parseFloat(item.monthly_rent).toLocaleString()} / month
            </Text>
          </View>
          
          <View style={styles.infoRow}>
            <Ionicons name="calendar-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <Text style={[styles.infoText, isDarkMode && styles.textMuted]}>
              {new Date(item.start_date).toLocaleDateString()} - {new Date(item.end_date).toLocaleDateString()}
            </Text>
          </View>
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Active Tenants</Text>
        <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Manage your current leases</Text>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : leases.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Ionicons name="people-outline" size={80} color={isDarkMode ? '#334155' : '#cbd5e1'} />
          <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Active Tenants</Text>
          <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>
            You don't have any active leases yet. Approve applications to add tenants.
          </Text>
        </View>
      ) : (
        <FlatList
          data={leases}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderLeaseItem}
          contentContainerStyle={styles.listContainer}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" colors={['#e11d48']} />
          }
        />
      )}
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
    borderBottomWidth: 1,
    borderBottomColor: '#f8fafc',
  },
  tenantInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  avatarContainer: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#f1f5f9',
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
    marginRight: 12,
  },
  avatar: {
    width: '100%',
    height: '100%',
  },
  tenantText: {
    flex: 1,
  },
  tenantName: {
    fontSize: 16,
    fontWeight: '700',
    color: '#0f172a',
  },
  tenantEmail: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 2,
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
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
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
});
