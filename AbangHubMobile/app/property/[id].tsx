import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, ScrollView, Image, TouchableOpacity, SafeAreaView, Dimensions } from 'react-native';
import { useLocalSearchParams, router, Stack } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import MapView, { Marker } from 'react-native-maps';
import apiClient from '../../src/api/client';
import { Property } from '../../src/types';

const { width } = Dimensions.get('window');

export default function PropertyDetails() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const [property, setProperty] = useState<Property | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    fetchPropertyDetails();
  }, [id]);

  const fetchPropertyDetails = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get(`/properties/${id}`);
      setProperty(response.data.data);
    } catch (error) {
      console.error('Error fetching property details:', error);
      alert('Failed to load property details.');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  if (!property) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.errorText}>Property not found.</Text>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const primaryImage = property.images?.find(img => img.is_primary)?.image_path 
    || property.primary_image?.image_path 
    || property.images?.[0]?.image_path 
    || 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1080&auto=format&fit=crop';

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>
        
        {/* Header Image */}
        <View style={styles.imageContainer}>
          <Image source={{ uri: primaryImage }} style={styles.mainImage} />
          <TouchableOpacity style={styles.backIconBtn} onPress={() => router.back()}>
            <Ionicons name="arrow-back" size={24} color="#0f172a" />
          </TouchableOpacity>
        </View>

        {/* Content */}
        <View style={styles.contentContainer}>
          <View style={styles.titleRow}>
            <Text style={styles.title}>{property.title}</Text>
          </View>
          
          <Text style={styles.price}>₱{Number(property.monthly_rent).toLocaleString()} <Text style={styles.priceUnit}>/ month</Text></Text>
          
          <View style={styles.locationRow}>
            <Ionicons name="location" size={16} color="#64748b" />
            <Text style={styles.locationText}>{property.address}, {property.city}, {property.province}</Text>
          </View>

          {/* Quick Stats */}
          <View style={styles.statsContainer}>
            <View style={styles.statBox}>
              <Ionicons name="home" size={20} color="#0f172a" />
              <Text style={styles.statText}>{property.property_type.replace('_', ' ').toUpperCase()}</Text>
            </View>
            <View style={styles.statBox}>
              <Ionicons name="bed" size={20} color="#0f172a" />
              <Text style={styles.statText}>{property.bedrooms} Bed</Text>
            </View>
            <View style={styles.statBox}>
              <Ionicons name="water" size={20} color="#0f172a" />
              <Text style={styles.statText}>{property.bathrooms} Bath</Text>
            </View>
          </View>

          {/* Divider */}
          <View style={styles.divider} />

          {/* Description */}
          <Text style={styles.sectionTitle}>Description</Text>
          <Text style={styles.description}>{property.description || 'No description available for this property.'}</Text>

          {/* Divider */}
          <View style={styles.divider} />

          {/* Location / Map */}
          <Text style={styles.sectionTitle}>Location</Text>
          <View style={styles.mapContainer}>
            <MapView
              style={styles.map}
              initialRegion={{
                latitude: Number(property.latitude) || 6.7483, // Digos City center fallback
                longitude: Number(property.longitude) || 125.3560,
                latitudeDelta: 0.01,
                longitudeDelta: 0.01,
              }}
              scrollEnabled={false}
              zoomEnabled={false}
            >
              <Marker
                coordinate={{
                  latitude: Number(property.latitude) || 6.7483,
                  longitude: Number(property.longitude) || 125.3560,
                }}
                title={property.title}
                description={property.address}
              />
            </MapView>
          </View>

          {/* Divider */}
          <View style={styles.divider} />

          {/* Owner Info */}
          <Text style={styles.sectionTitle}>Listed By</Text>
          <View style={styles.ownerBox}>
            <View style={styles.ownerAvatar}>
              <Text style={styles.ownerInitial}>{property.owner?.first_name?.[0] || 'L'}</Text>
            </View>
            <View style={styles.ownerInfo}>
              <Text style={styles.ownerName}>{property.owner?.first_name} {property.owner?.last_name}</Text>
              <Text style={styles.ownerRole}>Property Owner</Text>
            </View>
            <TouchableOpacity style={styles.contactBtn}>
              <Ionicons name="chatbubble-ellipses" size={20} color="#e11d48" />
            </TouchableOpacity>
          </View>
        </View>

      </ScrollView>

      {/* Sticky Bottom Bar */}
      <View style={styles.bottomBar}>
        <View style={styles.bottomPrice}>
          <Text style={styles.bottomPriceLabel}>Total Price</Text>
          <Text style={styles.bottomPriceValue}>₱{Number(property.monthly_rent).toLocaleString()}</Text>
        </View>
        <TouchableOpacity style={styles.rentButton} onPress={() => alert('Application submitted successfully!')}>
          <Text style={styles.rentButtonText}>Rent Now</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
  },
  container: {
    flex: 1,
    backgroundColor: '#ffffff',
  },
  scrollContent: {
    paddingBottom: 100, // Make room for bottom bar
  },
  imageContainer: {
    width: '100%',
    height: 300,
    position: 'relative',
  },
  mainImage: {
    width: '100%',
    height: '100%',
  },
  backIconBtn: {
    position: 'absolute',
    top: 48,
    left: 20,
    backgroundColor: '#ffffff',
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 4,
  },
  contentContainer: {
    padding: 24,
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    backgroundColor: '#ffffff',
    marginTop: -24,
  },
  titleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 8,
  },
  title: {
    fontSize: 24,
    fontWeight: '800',
    color: '#0f172a',
    flex: 1,
  },
  price: {
    fontSize: 26,
    fontWeight: '900',
    color: '#e11d48',
    marginBottom: 12,
  },
  priceUnit: {
    fontSize: 16,
    fontWeight: '600',
    color: '#64748b',
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 24,
  },
  locationText: {
    fontSize: 14,
    color: '#64748b',
    marginLeft: 6,
    flex: 1,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  statBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    padding: 16,
    borderRadius: 16,
    alignItems: 'center',
    marginHorizontal: 4,
  },
  statText: {
    marginTop: 8,
    fontSize: 12,
    fontWeight: '700',
    color: '#334155',
  },
  divider: {
    height: 1,
    backgroundColor: '#e2e8f0',
    marginVertical: 24,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#0f172a',
    marginBottom: 12,
  },
  description: {
    fontSize: 15,
    color: '#475569',
    lineHeight: 24,
  },
  mapContainer: {
    height: 200,
    width: '100%',
    borderRadius: 16,
    overflow: 'hidden',
    marginTop: 8,
  },
  map: {
    width: '100%',
    height: '100%',
  },
  ownerBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
    padding: 16,
    borderRadius: 16,
  },
  ownerAvatar: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#0ea5e9',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  ownerInitial: {
    color: '#ffffff',
    fontSize: 20,
    fontWeight: 'bold',
  },
  ownerInfo: {
    flex: 1,
  },
  ownerName: {
    fontSize: 16,
    fontWeight: '700',
    color: '#0f172a',
  },
  ownerRole: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 2,
  },
  contactBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#ffe4e6',
    justifyContent: 'center',
    alignItems: 'center',
  },
  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#ffffff',
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 24,
    paddingTop: 16,
    paddingBottom: 32, // safe area padding
    borderTopWidth: 1,
    borderTopColor: '#e2e8f0',
  },
  bottomPrice: {
    flex: 1,
  },
  bottomPriceLabel: {
    fontSize: 12,
    color: '#64748b',
    fontWeight: '600',
    marginBottom: 4,
  },
  bottomPriceValue: {
    fontSize: 22,
    fontWeight: '900',
    color: '#0f172a',
  },
  rentButton: {
    backgroundColor: '#e11d48',
    paddingHorizontal: 32,
    paddingVertical: 16,
    borderRadius: 16,
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  rentButtonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  errorText: {
    fontSize: 18,
    color: '#64748b',
    marginBottom: 16,
  },
  backButton: {
    backgroundColor: '#0f172a',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  backButtonText: {
    color: '#ffffff',
    fontWeight: '600',
  },
});
