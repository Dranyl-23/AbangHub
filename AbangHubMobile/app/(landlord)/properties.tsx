import React, { useState, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator, TouchableOpacity, Image, RefreshControl, Alert } from 'react-native';
import { router, useFocusEffect } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { Property } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function LandlordPropertiesScreen() {
  const { isDarkMode } = useTheme();
  const [properties, setProperties] = useState<Property[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchProperties = async () => {
    try {
      const response = await apiClient.get('/landlord/properties');
      setProperties(response.data.data);
    } catch (error) {
      console.error('Error fetching landlord properties', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchProperties();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchProperties();
  };

  const deleteProperty = async (id: number) => {
    Alert.alert(
      'Delete Property',
      'Are you sure you want to delete this property?',
      [
        { text: 'Cancel', style: 'cancel' },
        { 
          text: 'Delete', 
          style: 'destructive',
          onPress: async () => {
            try {
              await apiClient.delete(`/properties/${id}`);
              fetchProperties();
            } catch (error) {
              Alert.alert('Error', 'Failed to delete property.');
            }
          }
        }
      ]
    );
  };

  const toggleStatus = async (id: number, currentStatus: string) => {
    const newStatus = currentStatus === 'available' ? 'rented' : 'available';
    try {
      await apiClient.put(`/properties/${id}`, { status: newStatus });
      fetchProperties();
    } catch (error) {
      Alert.alert('Error', 'Failed to update status.');
    }
  };

  const renderPropertyItem = ({ item }: { item: Property }) => {
    const imageUrl = item.primary_image?.image_path || item.primaryImage?.image_path || 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1080&auto=format&fit=crop';
    const fullImageUrl = imageUrl.startsWith('http') ? imageUrl : `${apiClient.defaults.baseURL?.replace('/api', '')}${imageUrl}`;
    
    return (
      <TouchableOpacity 
        style={[styles.card, isDarkMode && styles.cardDark]} 
        onPress={() => router.push(`/property/${item.id}` as any)}
        activeOpacity={0.9}
      >
        <View style={styles.imageContainer}>
          <Image 
            source={{ uri: fullImageUrl }} 
            style={styles.cardImage} 
            resizeMode="cover"
          />
          <View style={[styles.statusBadge, item.status === 'available' ? styles.statusAvailable : styles.statusRented]}>
            <Text style={styles.statusText}>{item.status === 'available' ? 'Available' : 'Rented'}</Text>
          </View>
        </View>
        <View style={styles.cardContent}>
          <View style={styles.priceRow}>
            <Text style={[styles.price, isDarkMode && styles.textDark]}>₱{parseFloat(item.monthly_rent.toString()).toLocaleString()}</Text>
            <Text style={styles.perMonth}>/month</Text>
          </View>
          <Text style={[styles.title, isDarkMode && styles.textDark]} numberOfLines={1}>{item.title}</Text>
          <View style={styles.locationRow}>
            <Ionicons name="location-outline" size={16} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              <Text style={[styles.location, isDarkMode && styles.textMuted]} numberOfLines={1}>
                {item.address ? `${item.address}, ` : ''}{item.barangay ? `${item.barangay}, ` : ''}{item.city}
              </Text>
          </View>
          
          <View style={[styles.actionsRow, isDarkMode && styles.actionsRowDark]}>
            <TouchableOpacity 
              style={styles.statusButton} 
              onPress={() => toggleStatus(item.id, item.status)}
            >
              <Ionicons name={item.status === 'available' ? 'lock-closed-outline' : 'lock-open-outline'} size={16} color="#0f172a" />
              <Text style={styles.actionText}>{item.status === 'available' ? 'Mark Occupied' : 'Mark Available'}</Text>
            </TouchableOpacity>
            
            <View style={styles.rightActions}>
              <TouchableOpacity 
                style={[styles.iconButton, styles.editButton]} 
                onPress={() => router.push(`/(landlord)/edit-property/${item.id}` as any)}
              >
                <Ionicons name="pencil" size={16} color="#fff" />
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.iconButton, styles.deleteButton]} 
                onPress={() => deleteProperty(item.id)}
              >
                <Ionicons name="trash" size={16} color="#fff" />
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>My Properties</Text>
        <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Manage your listings</Text>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : (
        <FlatList
          data={properties}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderPropertyItem}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" />
          }
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="home-outline" size={64} color={isDarkMode ? '#334155' : '#cbd5e1'} />
              <Text style={[styles.emptyText, isDarkMode && styles.textDark]}>No properties yet.</Text>
              <Text style={[styles.emptySubText, isDarkMode && styles.textMuted]}>Add your first property to start earning.</Text>
            </View>
          }
        />
      )}

      <TouchableOpacity 
        style={styles.fab} 
        onPress={() => router.push('/(landlord)/add-property' as any)}
        activeOpacity={0.8}
      >
        <Ionicons name="add" size={32} color="#fff" />
      </TouchableOpacity>
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
    paddingBottom: 100, // extra padding for FAB
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    marginBottom: 20,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
  },
  cardDark: {
    backgroundColor: '#1e293b',
  },
  imageContainer: {
    height: 180,
    width: '100%',
    position: 'relative',
  },
  cardImage: {
    width: '100%',
    height: '100%',
  },
  statusBadge: {
    position: 'absolute',
    top: 12,
    right: 12,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
  },
  statusAvailable: {
    backgroundColor: '#10b981',
  },
  statusRented: {
    backgroundColor: '#64748b',
  },
  statusText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  cardContent: {
    padding: 16,
  },
  priceRow: {
    flexDirection: 'row',
    alignItems: 'baseline',
    marginBottom: 8,
  },
  price: {
    fontSize: 20,
    fontWeight: '700',
    color: '#e11d48',
  },
  perMonth: {
    fontSize: 14,
    color: '#64748b',
    marginLeft: 4,
  },
  title: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
    marginBottom: 8,
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  location: {
    fontSize: 14,
    color: '#64748b',
    marginLeft: 4,
    flex: 1,
  },
  actionsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
  },
  actionsRowDark: {
    borderTopColor: '#334155',
  },
  statusButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 6,
    gap: 4,
  },
  actionText: {
    fontSize: 12,
    fontWeight: '500',
    color: '#0f172a',
  },
  rightActions: {
    flexDirection: 'row',
    gap: 8,
  },
  iconButton: {
    width: 32,
    height: 32,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  editButton: {
    backgroundColor: '#3b82f6',
  },
  deleteButton: {
    backgroundColor: '#ef4444',
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
});
