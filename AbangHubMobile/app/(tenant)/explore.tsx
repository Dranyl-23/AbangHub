import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator, TouchableOpacity, RefreshControl, Image, ImageBackground, StatusBar, TextInput, ScrollView } from 'react-native';
import { router, useFocusEffect, Redirect } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';
import * as SecureStore from 'expo-secure-store';
import { Ionicons } from '@expo/vector-icons';
import apiClient from '../../src/api/client';
import { Property, User } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

export default function Home() {
  const { isDarkMode } = useTheme();
  const [properties, setProperties] = useState<Property[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [user, setUser] = useState<User | null>(null);
  const [isLoggedIn, setIsLoggedIn] = useState<boolean>(false);
  
  const [searchQuery, setSearchQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState('All');

  const checkLoginStatus = async () => {
    try {
      const token = await SecureStore.getItemAsync('userToken');
      const userData = await SecureStore.getItemAsync('userData');
      
      if (token && userData) {
        setIsLoggedIn(true);
        setUser(JSON.parse(userData));
        fetchProperties();
      } else {
        setIsLoggedIn(false);
        setLoading(false);
      }
    } catch (error) {
      console.error(error);
      setLoading(false);
    }
  };

  // Check login status whenever the screen comes into focus
  useFocusEffect(
    useCallback(() => {
      checkLoginStatus();
    }, [])
  );

  const fetchProperties = async () => {
    try {
      const response = await apiClient.get('/properties');
      setProperties(response.data.data); // Assuming Laravel API Resource returns data array
    } catch (error) {
      console.error('Error fetching properties', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    fetchProperties();
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  if (!isLoggedIn) {
    return <Redirect href="/landing" />;
  }

  const toggleSaveProperty = async (id: number) => {
    if (!isLoggedIn) {
      router.push('/login' as any);
      return;
    }

    // Optimistic UI update
    setProperties(prev => prev.map(p => p.id === id ? { ...p, is_saved: !p.is_saved } : p));

    try {
      await apiClient.post(`/favorites/${id}`);
    } catch (error) {
      console.error('Failed to toggle save property', error);
      // Revert on failure
      setProperties(prev => prev.map(p => p.id === id ? { ...p, is_saved: !p.is_saved } : p));
    }
  };

  const filteredProperties = properties.filter(prop => {
    const matchesSearch = prop.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                          prop.city.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = activeCategory === 'All' || prop.property_type.toLowerCase() === activeCategory.toLowerCase();
    return matchesSearch && matchesCategory;
  });

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
            <Ionicons name={item.is_saved ? "heart" : "heart-outline"} size={22} color={item.is_saved ? "#e11d48" : "#ffffff"} />
          </TouchableOpacity>
        </View>

        <View style={styles.cardContent}>
          <View style={styles.cardHeader}>
            <Text style={[styles.propertyTitle, isDarkMode && styles.propertyTitleDark]} numberOfLines={1}>{item.title}</Text>
            <Text style={styles.price}>₱{Number(item.monthly_rent).toLocaleString()}<Text style={styles.priceInterval}>/mo</Text></Text>
          </View>

          <View style={styles.locationContainer}>
            <Ionicons name="location-sharp" size={16} color="#64748b" />
            <Text style={styles.locationText} numberOfLines={1}>
              {item.address ? `${item.address}, ` : ''}{item.barangay ? `${item.barangay}, ` : ''}{item.city}
            </Text>
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

  return (
    <View style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <View>
          <Text style={[styles.greeting, isDarkMode && styles.textDark]}>Hello, {user?.full_name || user?.username}</Text>
          <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Available Properties</Text>
        </View>
      </View>

      <View style={[styles.searchContainer, isDarkMode && styles.searchContainerDark]}>
        <Ionicons name="search" size={20} color={isDarkMode ? "#64748b" : "#94a3b8"} style={styles.searchIcon} />
        <TextInput
          style={[styles.searchInput, isDarkMode && styles.searchInputDark]}
          placeholder="Search properties or city..."
          placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
          value={searchQuery}
          onChangeText={setSearchQuery}
        />
      </View>

      <View style={styles.categoriesWrapper}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.categoriesContainer}>
          {['All', 'Apartment', 'House', 'Boarding House', 'Commercial'].map((category) => (
            <TouchableOpacity 
              key={category}
              style={[
                styles.categoryChip, 
                activeCategory === category && styles.categoryChipActive,
                isDarkMode && activeCategory !== category && styles.categoryChipDark
              ]}
              onPress={() => setActiveCategory(category)}
            >
              <Text style={[
                styles.categoryText, 
                activeCategory === category && styles.categoryTextActive,
                isDarkMode && activeCategory !== category && styles.categoryTextDark
              ]}>{category}</Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      <FlatList
        data={filteredProperties}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderPropertyItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={['#e11d48']} />
        }
        ListEmptyComponent={
          <Text style={[styles.emptyText, isDarkMode && styles.textDark]}>No properties available at the moment.</Text>
        }
      />

      {user?.role === 'landlord' && (
        <TouchableOpacity 
          style={styles.fab} 
          onPress={() => router.push('/landlord/add-property' as any)}
        >
          <Ionicons name="add" size={32} color="#ffffff" />
        </TouchableOpacity>
      )}
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
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 20,
    backgroundColor: '#ffffff',
  },
  headerDark: {
    backgroundColor: '#1e293b',
  },
  greeting: {
    fontSize: 14,
    color: '#64748b',
    fontWeight: '500',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#0f172a',
    marginTop: 4,
  },
  textDark: {
    color: '#f8fafc',
  },
  landingBackground: {
    flex: 1,
    width: '100%',
    height: '100%',
  },
  overlay: {
    flex: 1,
    justifyContent: 'flex-end',
    padding: 32,
    paddingBottom: 64,
  },
  landingContent: {
    width: '100%',
    alignItems: 'center',
  },
  brandingContainer: {
    alignItems: 'center',
    marginBottom: 64,
  },
  logoWrapper: {
    width: 96,
    height: 96,
    borderRadius: 48,
    backgroundColor: '#ffffff',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.3,
    shadowRadius: 2,
    elevation: 2,
  },
  fab: {
    position: 'absolute',
    width: 60,
    height: 60,
    alignItems: 'center',
    justifyContent: 'center',
    right: 24,
    bottom: 24,
    backgroundColor: '#e11d48',
    borderRadius: 30,
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 6,
  },
  landingLogo: {
    width: 88,
    height: 88,
    borderRadius: 44,
  },
  landingTitle: {
    fontSize: 52,
    fontWeight: '900',
    color: '#ffffff',
    marginBottom: 8,
    letterSpacing: -1.5,
    textAlign: 'center',
  },
  landingSubtitle: {
    fontSize: 18,
    color: '#cbd5e1',
    lineHeight: 28,
    textAlign: 'center',
    paddingHorizontal: 20,
  },
  landingButton: {
    width: '100%',
    backgroundColor: '#e11d48',
    paddingVertical: 18,
    borderRadius: 16,
    alignItems: 'center',
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 5,
  },
  landingButtonText: {
    color: '#ffffff',
    fontSize: 18,
    fontWeight: 'bold',
    letterSpacing: 0.5,
  },
  listContent: {
    padding: 16,
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
  emptyText: {
    textAlign: 'center',
    color: '#64748b',
    marginTop: 32,
    fontSize: 16,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    marginHorizontal: 16,
    marginTop: 16,
    paddingHorizontal: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    height: 50,
  },
  searchContainerDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
    color: '#0f172a',
  },
  searchInputDark: {
    color: '#f8fafc',
  },
  categoriesWrapper: {
    marginTop: 16,
    marginBottom: 8,
  },
  categoriesContainer: {
    paddingHorizontal: 16,
    gap: 8,
  },
  categoryChip: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  categoryChipActive: {
    backgroundColor: '#0f172a',
    borderColor: '#0f172a',
  },
  categoryChipDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  categoryText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#64748b',
  },
  categoryTextActive: {
    color: '#ffffff',
  },
  categoryTextDark: {
    color: '#94a3b8',
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
  }
});
