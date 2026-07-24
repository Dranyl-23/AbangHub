import React, { useState, useEffect, useRef } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, ActivityIndicator, Alert, Image } from 'react-native';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import * as Location from 'expo-location';
import MapView, { Marker } from 'react-native-maps';
import apiClient from '../../../src/api/client';
import { useTheme } from '../../../src/context/ThemeContext';

export default function EditPropertyScreen() {
  const { isDarkMode } = useTheme();
  const { id } = useLocalSearchParams<{ id: string }>();
  
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [monthlyRent, setMonthlyRent] = useState('');
  const [city, setCity] = useState('');
  const [barangay, setBarangay] = useState('');
  const [address, setAddress] = useState('');
  const [propertyType, setPropertyType] = useState('apartment');
  const [bedrooms, setBedrooms] = useState('1');
  const [bathrooms, setBathrooms] = useState('1');
  const [amenities, setAmenities] = useState<string[]>([]);
  const [image, setImage] = useState<string | null>(null);
  const [location, setLocation] = useState<{latitude: number, longitude: number} | null>(null);
  const [geocodeStatus, setGeocodeStatus] = useState<'idle' | 'searching' | 'found' | 'not_found'>('idle');
  const [userTypedLocation, setUserTypedLocation] = useState(false);
  const mapRef = useRef<MapView>(null);

  const AVAILABLE_AMENITIES = [
    'WiFi', 'Air Conditioning', 'Kitchen', 'Parking', 
    'Swimming Pool', 'Gym', 'Pet Friendly', 'Balcony'
  ];

  const toggleAmenity = (amenity: string) => {
    if (amenities.includes(amenity)) {
      setAmenities(amenities.filter(a => a !== amenity));
    } else {
      setAmenities([...amenities, amenity]);
    }
  };

  useEffect(() => {
    const fetchProperty = async () => {
      try {
        const response = await apiClient.get(`/properties/${id}`);
        const prop = response.data.data;
        setTitle(prop.title);
        setDescription(prop.description);
        setMonthlyRent(prop.monthly_rent.toString());
        setCity(prop.city);
        setBarangay(prop.barangay);
        setAddress(prop.address);
        setPropertyType(prop.property_type);
        setBedrooms(prop.bedrooms?.toString() || '0');
        setBathrooms(prop.bathrooms?.toString() || '0');
        if (prop.amenities) {
          setAmenities(prop.amenities.map((a: any) => a.amenity_name));
        }
        if (prop.latitude && prop.longitude) {
          setLocation({ latitude: Number(prop.latitude), longitude: Number(prop.longitude) });
          setGeocodeStatus('found');
        }
        
        const primaryImage = prop.images?.find((img: any) => img.is_primary)?.image_path || prop.primary_image?.image_path;
        if (primaryImage) {
          const fullImageUrl = primaryImage.startsWith('http') ? primaryImage : `${apiClient.defaults.baseURL?.replace('/api', '')}${primaryImage}`;
          setImage(fullImageUrl);
        }
      } catch (error) {
        console.error('Failed to fetch property details', error);
        Alert.alert('Error', 'Failed to load property details.');
      } finally {
        setLoading(false);
      }
    };
    fetchProperty();
  }, [id]);

  useEffect(() => {
    if (!userTypedLocation) return;

    const timer = setTimeout(async () => {
      const fullSearchAddress = `${address} ${barangay} ${city}`.trim();
      if (fullSearchAddress.length > 5) {
        setGeocodeStatus('searching');
        try {
          // Request permissions first
          const { status } = await Location.requestForegroundPermissionsAsync();
          if (status !== 'granted') {
            console.warn('Permission to access location was denied');
            setGeocodeStatus('not_found');
            return;
          }

          const results = await Location.geocodeAsync(fullSearchAddress);
          if (results.length > 0) {
            setLocation({ latitude: results[0].latitude, longitude: results[0].longitude });
            setGeocodeStatus('found');
            if (mapRef.current) {
              mapRef.current.animateToRegion({
                latitude: results[0].latitude,
                longitude: results[0].longitude,
                latitudeDelta: 0.01,
                longitudeDelta: 0.01,
              }, 1000);
            }
          } else {
            if (city) {
              const cityResults = await Location.geocodeAsync(city);
              if (cityResults.length > 0) {
                setLocation({ latitude: cityResults[0].latitude, longitude: cityResults[0].longitude });
                setGeocodeStatus('found');
                if (mapRef.current) {
                  mapRef.current.animateToRegion({
                    latitude: cityResults[0].latitude,
                    longitude: cityResults[0].longitude,
                    latitudeDelta: 0.05,
                    longitudeDelta: 0.05,
                  }, 1000);
                }
              } else {
                setGeocodeStatus('not_found');
              }
            } else {
              setGeocodeStatus('not_found');
            }
          }
        } catch (error) {
          console.error('Geocoding error:', error);
          setGeocodeStatus('not_found');
        }
      } else {
        setGeocodeStatus('idle');
      }
    }, 1500);

    return () => clearTimeout(timer);
  }, [address, barangay, city]);

  const pickImage = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 0.8,
    });

    if (!result.canceled) {
      setImage(result.assets[0].uri);
    }
  };

  const handleUpdateProperty = async () => {
    if (!title || !description || !monthlyRent || !city || !barangay || !address || !bedrooms || !bathrooms) {
      Alert.alert('Error', 'Please fill in all required fields.');
      return;
    }

    setSubmitting(true);
    try {
      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('monthly_rent', monthlyRent);
      formData.append('city', city);
      formData.append('barangay', barangay);
      formData.append('address', address);
      formData.append('property_type', propertyType);
      formData.append('bedrooms', bedrooms);
      formData.append('bathrooms', bathrooms);
      formData.append('amenities', JSON.stringify(amenities));
      formData.append('_method', 'PUT');
      if (location) {
        formData.append('latitude', String(location.latitude));
        formData.append('longitude', String(location.longitude));
      }

      if (image && !image.startsWith('http')) {
        const localUri = image;
        const filename = localUri.split('/').pop() || 'photo.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image`;

        formData.append('image', {
          uri: localUri,
          name: filename,
          type
        } as any);
      }

      await apiClient.post(`/properties/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      Alert.alert('Success', 'Property updated successfully!', [
        { text: 'OK', onPress: () => router.back() }
      ]);
    } catch (error: any) {
      const message = error.response?.data?.message || 'Failed to update property.';
      Alert.alert('Error', message);
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <View style={[styles.container, isDarkMode && styles.containerDark, { justifyContent: 'center', alignItems: 'center' }]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <Stack.Screen options={{ headerShown: false }} />
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Edit Property</Text>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <TouchableOpacity style={styles.imagePicker} onPress={pickImage}>
          {image ? (
            <Image source={{ uri: image }} style={styles.previewImage} />
          ) : (
            <View style={styles.imagePlaceholder}>
              <Ionicons name="camera-outline" size={48} color={isDarkMode ? '#94a3b8' : '#cbd5e1'} />
              <Text style={[styles.imagePlaceholderText, isDarkMode && styles.textMuted]}>Add Primary Photo</Text>
            </View>
          )}
        </TouchableOpacity>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Title</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            value={title}
            onChangeText={setTitle}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Property Type</Text>
          <View style={styles.typeSelector}>
            {['apartment', 'house', 'room', 'studio'].map((type) => (
              <TouchableOpacity
                key={type}
                style={[
                  styles.typeButton,
                  isDarkMode && styles.typeButtonDark,
                  propertyType === type && styles.typeButtonActive
                ]}
                onPress={() => setPropertyType(type)}
              >
                <Text style={[
                  styles.typeButtonText,
                  isDarkMode && styles.textDark,
                  propertyType === type && styles.typeButtonTextActive
                ]}>
                  {type.charAt(0).toUpperCase() + type.slice(1)}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Monthly Rent (₱)</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            keyboardType="numeric"
            value={monthlyRent}
            onChangeText={setMonthlyRent}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Street / Purok / Block</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            value={address}
            onChangeText={(text) => { setAddress(text); setUserTypedLocation(true); }}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Barangay</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            value={barangay}
            onChangeText={(text) => { setBarangay(text); setUserTypedLocation(true); }}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>City</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            value={city}
            onChangeText={(text) => { setCity(text); setUserTypedLocation(true); }}
          />
        </View>

        <View style={styles.formGroup}>
          <View style={styles.mapHeaderRow}>
            <Text style={[styles.label, isDarkMode && styles.textDark]}>Pin Location on Map</Text>
            {geocodeStatus === 'searching' && <Text style={styles.statusSearching}>Searching...</Text>}
            {geocodeStatus === 'found' && <Text style={styles.statusFound}>✓ Found</Text>}
            {geocodeStatus === 'not_found' && <Text style={styles.statusNotFound}>Not Found (Pin Manually)</Text>}
          </View>
          <View style={styles.mapContainer}>
            <MapView
              ref={mapRef}
              style={styles.map}
              initialRegion={{
                latitude: location?.latitude || 6.7490,
                longitude: location?.longitude || 125.3562,
                latitudeDelta: 0.05,
                longitudeDelta: 0.05,
              }}
              onPress={(e) => setLocation(e.nativeEvent.coordinate)}
            >
              {location && (
                <Marker 
                  coordinate={location}
                  draggable
                  onDragEnd={(e) => setLocation(e.nativeEvent.coordinate)}
                />
              )}
            </MapView>
          </View>
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Description</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark, styles.textArea]}
            multiline
            numberOfLines={4}
            value={description}
            onChangeText={setDescription}
          />
        </View>

        <View style={styles.rowGroup}>
          <View style={[styles.formGroup, { flex: 1, marginRight: 8 }]}>
            <Text style={[styles.label, isDarkMode && styles.textDark]}>Bedrooms</Text>
            <TextInput
              style={[styles.input, isDarkMode && styles.inputDark]}
              keyboardType="numeric"
              value={bedrooms}
              onChangeText={setBedrooms}
            />
          </View>
          <View style={[styles.formGroup, { flex: 1, marginLeft: 8 }]}>
            <Text style={[styles.label, isDarkMode && styles.textDark]}>Bathrooms</Text>
            <TextInput
              style={[styles.input, isDarkMode && styles.inputDark]}
              keyboardType="numeric"
              value={bathrooms}
              onChangeText={setBathrooms}
            />
          </View>
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Amenities</Text>
          <View style={styles.amenitiesContainer}>
            {AVAILABLE_AMENITIES.map((amenity) => {
              const isSelected = amenities.includes(amenity);
              return (
                <TouchableOpacity
                  key={amenity}
                  style={[
                    styles.amenityChip,
                    isDarkMode && styles.amenityChipDark,
                    isSelected && styles.amenityChipSelected
                  ]}
                  onPress={() => toggleAmenity(amenity)}
                >
                  <Text style={[
                    styles.amenityText,
                    isDarkMode && styles.amenityTextDark,
                    isSelected && styles.amenityTextSelected
                  ]}>
                    {amenity}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        <TouchableOpacity 
          style={styles.submitButton} 
          onPress={handleUpdateProperty}
          disabled={submitting}
        >
          {submitting ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitButtonText}>Save Changes</Text>
          )}
        </TouchableOpacity>
      </ScrollView>
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
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e2e8f0',
  },
  backButton: {
    marginRight: 16,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '600',
    color: '#0f172a',
  },
  textDark: {
    color: '#f8fafc',
  },
  textMuted: {
    color: '#94a3b8',
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 40,
  },
  imagePicker: {
    width: '100%',
    height: 200,
    backgroundColor: '#e2e8f0',
    borderRadius: 12,
    marginBottom: 24,
    overflow: 'hidden',
    justifyContent: 'center',
    alignItems: 'center',
  },
  previewImage: {
    width: '100%',
    height: '100%',
    resizeMode: 'cover',
  },
  imagePlaceholder: {
    alignItems: 'center',
  },
  imagePlaceholderText: {
    marginTop: 8,
    color: '#64748b',
    fontWeight: '500',
  },
  formGroup: {
    marginBottom: 20,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#334155',
    marginBottom: 8,
  },
  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 16,
    color: '#0f172a',
  },
  inputDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
    color: '#f8fafc',
  },
  textArea: {
    minHeight: 100,
    textAlignVertical: 'top',
  },
  typeSelector: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  typeButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    backgroundColor: '#fff',
  },
  typeButtonDark: {
    borderColor: '#334155',
    backgroundColor: '#1e293b',
  },
  typeButtonActive: {
    backgroundColor: '#e11d48',
    borderColor: '#e11d48',
  },
  typeButtonText: {
    color: '#64748b',
    fontWeight: '500',
  },
  typeButtonTextActive: {
    color: '#fff',
  },
  submitButton: {
    backgroundColor: '#e11d48',
    borderRadius: 8,
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: 12,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  mapContainer: {
    height: 250,
    width: '100%',
    borderRadius: 12,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    marginTop: 8,
  },
  map: {
    ...StyleSheet.absoluteFillObject,
  },
  helpText: {
    fontSize: 12,
    color: '#64748b',
    marginBottom: 8,
  },
  mapHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  statusSearching: {
    fontSize: 12,
    color: '#f59e0b',
    fontWeight: '500',
  },
  statusFound: {
    fontSize: 12,
    color: '#10b981',
    fontWeight: '500',
  },
  statusNotFound: {
    fontSize: 12,
    color: '#ef4444',
    fontWeight: '500',
  },
  rowGroup: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  amenitiesContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  amenityChip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: '#f1f5f9',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  amenityChipDark: {
    backgroundColor: '#334155',
    borderColor: '#475569',
  },
  amenityChipSelected: {
    backgroundColor: '#e11d48',
    borderColor: '#e11d48',
  },
  amenityText: {
    fontSize: 14,
    color: '#475569',
  },
  amenityTextDark: {
    color: '#cbd5e1',
  },
  amenityTextSelected: {
    color: '#ffffff',
    fontWeight: '500',
  },
});
