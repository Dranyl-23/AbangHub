import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, Image, TouchableOpacity, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import apiClient from '../../src/api/client';
import { Application } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function ApplicationsScreen() {
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
      setLoading(true);
      fetchApplications();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchApplications();
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'approved': return '#22c55e'; // Green
      case 'rejected': return '#ef4444'; // Red
      case 'pending': return '#f59e0b'; // Amber
      default: return '#64748b'; // Gray
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status.toLowerCase()) {
      case 'approved': return 'checkmark-circle';
      case 'rejected': return 'close-circle';
      case 'pending': return 'time';
      default: return 'help-circle';
    }
  };

  const renderApplicationItem = ({ item }: { item: Application }) => {
    const property = item.property;
    if (!property) return null;

    const imageUrl = property.primary_image?.image_path || property.primaryImage?.image_path || 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1080&auto=format&fit=crop';
    const fullImageUrl = imageUrl.startsWith('http') ? imageUrl : `${apiClient.defaults.baseURL?.replace('/api', '')}${imageUrl}`;

    return (
      <TouchableOpacity 
        style={[styles.card, isDarkMode && styles.cardDark]}
        onPress={() => router.push(`/property/${property.id}` as any)}
      >
        <Image source={{ uri: fullImageUrl }} style={styles.cardImage} />
        
        <View style={styles.cardContent}>
          <View style={styles.headerRow}>
            <Text style={[styles.title, isDarkMode && styles.textDark]} numberOfLines={1}>{property.title}</Text>
            <View style={[styles.statusBadge, { backgroundColor: `${getStatusColor(item.status)}20` }]}>
              <Ionicons name={getStatusIcon(item.status)} size={14} color={getStatusColor(item.status)} />
              <Text style={[styles.statusText, { color: getStatusColor(item.status) }]}>
                {item.status.toUpperCase()}
              </Text>
            </View>
          </View>
          
          <Text style={styles.location} numberOfLines={1}>
            <Ionicons name="location" size={14} /> {property.city}
          </Text>

          <View style={styles.detailsRow}>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Applied On</Text>
              <Text style={[styles.detailValue, isDarkMode && styles.textDark]}>
                {new Date(item.created_at).toLocaleDateString()}
              </Text>
            </View>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Move-in Date</Text>
              <Text style={[styles.detailValue, isDarkMode && styles.textDark]}>
                {item.move_in_date || 'Not specified'}
              </Text>
            </View>
            <View style={styles.detailItem}>
              <Text style={styles.detailLabel}>Price</Text>
              <Text style={styles.priceValue}>
                ₱{Number(property.monthly_rent).toLocaleString()}
              </Text>
            </View>
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  if (loading) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <View style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>My Applications</Text>
      </View>

      <FlatList
        data={applications}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderApplicationItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={['#e11d48']} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="document-text-outline" size={64} color={isDarkMode ? "#334155" : "#e2e8f0"} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Applications Yet</Text>
            <Text style={styles.emptySubtitle}>When you apply for a property, it will appear here.</Text>
          </View>
        }
      />
    </View>
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
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
  },
  header: {
    paddingTop: 60,
    paddingHorizontal: 20,
    paddingBottom: 20,
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
    fontWeight: '900',
    color: '#0f172a',
  },
  listContent: {
    padding: 16,
    paddingBottom: 100,
  },
  textDark: {
    color: '#f8fafc',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 60,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#0f172a',
    marginTop: 16,
  },
  emptySubtitle: {
    fontSize: 15,
    color: '#64748b',
    marginTop: 8,
    textAlign: 'center',
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    marginBottom: 16,
    flexDirection: 'row',
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 3,
  },
  cardDark: {
    backgroundColor: '#1e293b',
  },
  cardImage: {
    width: 100,
    height: '100%',
  },
  cardContent: {
    flex: 1,
    padding: 12,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 4,
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#0f172a',
    flex: 1,
    marginRight: 8,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 4,
  },
  statusText: {
    fontSize: 10,
    fontWeight: 'bold',
  },
  location: {
    fontSize: 13,
    color: '#64748b',
    marginBottom: 12,
  },
  detailsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
    paddingTop: 12,
  },
  detailItem: {
    flex: 1,
  },
  detailLabel: {
    fontSize: 11,
    color: '#94a3b8',
    marginBottom: 2,
  },
  detailValue: {
    fontSize: 12,
    fontWeight: '600',
    color: '#334155',
  },
  priceValue: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#e11d48',
  },
});
