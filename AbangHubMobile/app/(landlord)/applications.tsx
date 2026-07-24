import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, Image, TouchableOpacity, RefreshControl, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { Application } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function LandlordApplicationsScreen() {
  const { isDarkMode } = useTheme();
  const [applications, setApplications] = useState<Application[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchApplications = async () => {
    try {
      const response = await apiClient.get('/applications');
      setApplications(response.data.data);
    } catch (error) {
      console.error('Error fetching applications', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchApplications();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchApplications();
  };

  const updateApplicationStatus = async (id: number, status: 'approved' | 'rejected') => {
    try {
      await apiClient.put(`/applications/${id}`, { status });
      Alert.alert('Success', `Application ${status}!`);
      fetchApplications(); // Refresh list to reflect changes
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to update application.');
    }
  };

  const confirmUpdate = (id: number, status: 'approved' | 'rejected') => {
    Alert.alert(
      `Confirm ${status === 'approved' ? 'Approval' : 'Rejection'}`,
      `Are you sure you want to ${status.replace(/ed$/, 'e')} this application?`,
      [
        { text: 'Cancel', style: 'cancel' },
        { 
          text: status === 'approved' ? 'Approve' : 'Reject', 
          style: status === 'approved' ? 'default' : 'destructive',
          onPress: () => updateApplicationStatus(id, status)
        }
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'approved': return '#22c55e'; // Green
      case 'rejected': return '#ef4444'; // Red
      case 'pending': return '#f59e0b'; // Amber
      default: return '#64748b'; // Gray
    }
  };

  const renderApplicationItem = ({ item }: { item: Application }) => {
    const property = item.property;
    const tenant = item.user || item.tenant;
    if (!property || !tenant) return null;

    const imageUrl = property.primary_image?.image_path || property.primaryImage?.image_path || 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1080&auto=format&fit=crop';
    const fullImageUrl = imageUrl.startsWith('http') ? imageUrl : `${apiClient.defaults.baseURL?.replace('/api', '')}${imageUrl}`;

    return (
      <View style={[styles.card, isDarkMode && styles.cardDark]}>
        <View style={styles.cardHeader}>
          <Image source={{ uri: fullImageUrl }} style={styles.cardImage} />
          <View style={styles.headerInfo}>
            <Text style={[styles.title, isDarkMode && styles.textDark]} numberOfLines={1}>{property.title}</Text>
            <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
              <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>
                {item.status.toUpperCase()}
              </Text>
            </View>
          </View>
        </View>
        
        <View style={styles.cardContent}>
          <View style={styles.tenantRow}>
            <Ionicons name="person-circle-outline" size={32} color={isDarkMode ? '#94a3b8' : '#64748b'} />
            <View style={styles.tenantInfo}>
              <Text style={[styles.tenantName, isDarkMode && styles.textDark]}>{tenant.full_name || tenant.username || 'Tenant'}</Text>
              <Text style={[styles.tenantEmail, isDarkMode && styles.textMuted]}>{tenant.email}</Text>
            </View>
            <TouchableOpacity 
              style={styles.chatButton}
              onPress={() => router.push({ pathname: '/messages/[userId]', params: { userId: tenant.id, propertyId: property.id } } as any)}
            >
              <Ionicons name="chatbubble-ellipses" size={20} color="#fff" />
            </TouchableOpacity>
          </View>

          <View style={styles.detailsBox}>
            <Text style={[styles.messageTitle, isDarkMode && styles.textDark]}>Message from tenant:</Text>
            <Text style={[styles.messageText, isDarkMode && styles.textMuted]}>"{item.message}"</Text>
            <Text style={[styles.dateText, isDarkMode && styles.textMuted]}>Applied: {new Date(item.created_at).toLocaleDateString()}</Text>
          </View>

          {item.status === 'pending' && (
            <View style={styles.actionRow}>
              <TouchableOpacity 
                style={[styles.actionButton, styles.rejectButton]} 
                onPress={() => confirmUpdate(item.id, 'rejected')}
              >
                <Ionicons name="close" size={18} color="#ef4444" />
                <Text style={styles.rejectText}>Reject</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.actionButton, styles.approveButton]} 
                onPress={() => confirmUpdate(item.id, 'approved')}
              >
                <Ionicons name="checkmark" size={18} color="#fff" />
                <Text style={styles.approveText}>Approve</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Applications</Text>
        <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Manage tenant requests</Text>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : (
        <FlatList
          data={applications}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderApplicationItem}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" />
          }
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="document-text-outline" size={64} color={isDarkMode ? '#334155' : '#cbd5e1'} />
              <Text style={[styles.emptyText, isDarkMode && styles.textDark]}>No applications yet.</Text>
              <Text style={[styles.emptySubText, isDarkMode && styles.textMuted]}>When tenants apply for your properties, they will appear here.</Text>
            </View>
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
    paddingTop: 10,
    paddingBottom: 20,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: '700',
    color: '#0f172a',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#64748b',
  },
  textDark: {
    color: '#f8fafc',
  },
  textMuted: {
    color: '#94a3b8',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    padding: 20,
    paddingBottom: 40,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    marginBottom: 20,
    overflow: 'hidden',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  cardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  cardImage: {
    width: 50,
    height: 50,
    borderRadius: 8,
    marginRight: 12,
  },
  headerInfo: {
    flex: 1,
  },
  title: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
    marginBottom: 4,
  },
  statusBadge: {
    alignSelf: 'flex-start',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 10,
    fontWeight: '700',
  },
  cardContent: {
    padding: 16,
  },
  tenantRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  tenantInfo: {
    flex: 1,
    marginLeft: 12,
  },
  tenantName: {
    fontSize: 15,
    fontWeight: '600',
    color: '#0f172a',
  },
  tenantEmail: {
    fontSize: 13,
    color: '#64748b',
  },
  chatButton: {
    backgroundColor: '#3b82f6',
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  detailsBox: {
    backgroundColor: '#f8fafc',
    padding: 12,
    borderRadius: 8,
    marginBottom: 16,
  },
  messageTitle: {
    fontSize: 12,
    fontWeight: '600',
    color: '#334155',
    marginBottom: 4,
  },
  messageText: {
    fontSize: 14,
    color: '#475569',
    fontStyle: 'italic',
    marginBottom: 8,
  },
  dateText: {
    fontSize: 12,
    color: '#94a3b8',
    textAlign: 'right',
  },
  actionRow: {
    flexDirection: 'row',
    gap: 12,
  },
  actionButton: {
    flex: 1,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
  },
  rejectButton: {
    backgroundColor: '#fff',
    borderColor: '#ef4444',
  },
  rejectText: {
    color: '#ef4444',
    fontWeight: '600',
    marginLeft: 6,
  },
  approveButton: {
    backgroundColor: '#22c55e',
    borderColor: '#22c55e',
  },
  approveText: {
    color: '#fff',
    fontWeight: '600',
    marginLeft: 6,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#0f172a',
    marginTop: 16,
    marginBottom: 8,
  },
  emptySubText: {
    fontSize: 14,
    color: '#64748b',
    textAlign: 'center',
  },
});
