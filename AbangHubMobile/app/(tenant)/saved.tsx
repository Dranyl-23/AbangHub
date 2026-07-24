import React, { useState, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator, TouchableOpacity, Image, RefreshControl } from 'react-native';
import { router, useFocusEffect } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import apiClient from '../../src/api/client';
import { Property } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function SavedScreen() {
  const { isDarkMode } = useTheme();
  const [properties, setProperties] = useState<Property[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchSavedProperties = async () => {
    try {
      const response = await apiClient.get('/favorites');
      setProperties(response.data.data);
    } catch (error) {
      console.error('Error fetching saved properties', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      setLoading(true);
      fetchSavedProperties();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchSavedProperties();
  };

  const toggleSaveProperty = async (id: number) => {
    // Optimistically remove from list
    setProperties(prev => prev.filter(p => p.id !== id));

    try {
      await apiClient.post(`/favorites/${id}`);
    } catch (error) {
      console.error('Failed to toggle save property', error);
      // If it fails, refresh the list to get true state
      fetchSavedProperties();
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
          />
          <View style={styles.typeBadge}>
            <Text style={styles.typeBadgeText}>{item.property_type.toUpperCase()}</Text>
          </View>
          <TouchableOpacity 
            style={styles.heartButton}
            onPress={() => toggleSaveProperty(item.id)}
          >
            <Ionicons name="heart" size={22} color="#e11d48" />
          </TouchableOpacity>
        </View>

        <View style={styles.cardContent}>
          <View style={styles.cardHeader}>
            <Text style={[styles.propertyTitle, isDarkMode && styles.propertyTitleDark]} numberOfLines={1}>{item.title}</Text>
            <Text style={styles.price}>₱{Number(item.monthly_rent).toLocaleString()}<Text style={styles.priceInterval}>/mo</Text></Text>
          </View>

          <View style={styles.locationContainer}>
            <Ionicons name="location" size={16} color="#64748b" />
            <Text style={styles.locationText} numberOfLines={1}>{item.city}</Text>
          </View>
          
          <View style={styles.quickInfoRow}>
            <View style={styles.quickInfoItem}>
              <Ionicons name="bed-outline" size={16} color="#64748b" />
              <Text style={styles.quickInfoText}>{item.bedrooms || 0} Beds</Text>
            </View>
            <View style={styles.quickInfoItem}>
              <Ionicons name="water-outline" size={16} color="#64748b" />
              <Text style={styles.quickInfoText}>{item.bathrooms || 0} Baths</Text>
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
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Saved Properties</Text>
      </View>

      <FlatList
        data={properties}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderPropertyItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={['#e11d48']} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="heart-outline" size={64} color={isDarkMode ? "#334155" : "#e2e8f0"} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Saved Properties</Text>
            <Text style={styles.emptySubtitle}>Properties you heart will appear here.</Text>
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
  textDark: {
    color: '#f8fafc',
  },
  listContent: {
    padding: 16,
    paddingBottom: 100,
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
    marginBottom: 20,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 12,
    elevation: 5,
  },
  cardDark: {
    backgroundColor: '#1e293b',
  },
  imageContainer: {
    overflow: 'hidden',
  },
  cardImage: {
    width: '100%',
    height: 180,
  },
  typeBadge: {
    position: 'absolute',
    top: 12,
    right: 12,
    backgroundColor: 'rgba(15, 23, 42, 0.75)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
  },
  typeBadgeText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: 'bold',
    letterSpacing: 1,
  },
  heartButton: {
    position: 'absolute',
    top: 12,
    left: 12,
    backgroundColor: 'rgba(15, 23, 42, 0.4)',
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.3)',
  },
  cardContent: {
    padding: 16,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  propertyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#0f172a',
    flex: 1,
    marginRight: 8,
  },
  propertyTitleDark: {
    color: '#f8fafc',
  },
  price: {
    fontSize: 20,
    fontWeight: '900',
    color: '#e11d48',
  },
  priceInterval: {
    fontSize: 14,
    color: '#64748b',
    fontWeight: '600',
  },
  locationContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  locationText: {
    fontSize: 14,
    color: '#64748b',
    marginLeft: 4,
  },
  quickInfoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
    paddingTop: 12,
    gap: 16,
  },
  quickInfoItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  quickInfoText: {
    fontSize: 13,
    color: '#64748b',
    marginLeft: 6,
    fontWeight: '500',
  },
});
