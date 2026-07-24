import React, { useState, useEffect, useRef } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, ActivityIndicator, Alert, Image } from 'react-native';
import { router, Stack } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import * as Location from 'expo-location';
import MapView, { Marker } from 'react-native-maps';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function AddPropertyScreen() {
  const { isDarkMode } = useTheme();
  
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
  // Start with no specific pin
  const [location, setLocation] = useState<{latitude: number, longitude: number} | null>(null);
  const [loading, setLoading] = useState(false);
  const [geocodeStatus, setGeocodeStatus] = useState<'idle' | 'searching' | 'found' | 'not_found'>('idle');
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
    }, 1500); // 1.5s delay to avoid spamming while typing

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

  const handleAddProperty = async () => {
    if (!title.trim() || !description.trim() || !monthlyRent || !city || !barangay || !address) {
      Alert.alert('Error', 'Please fill in all required fields.');
      return;
    }

    const rent = parseFloat(monthlyRent);
    if (isNaN(rent) || rent <= 0) {
      Alert.alert('Error', 'Monthly rent must be a valid amount greater than 0.');
      return;
    }
    if (rent > 1000000) {
      Alert.alert('Error', 'Monthly rent seems too high. Please double-check.');
      return;
    }

    const beds = parseInt(bedrooms, 10);
    const baths = parseInt(bathrooms, 10);
    if (isNaN(beds) || beds < 0 || beds > 50) {
      Alert.alert('Error', 'Please enter a valid number of bedrooms (0-50).');
      return;
    }
    if (isNaN(baths) || baths < 0 || baths > 50) {
      Alert.alert('Error', 'Please enter a valid number of bathrooms (0-50).');
      return;
    }

    setLoading(true);
    try {
      const formData = new FormData();
      formData.append('title', title.trim());
      formData.append('description', description.trim());
      formData.append('monthly_rent', String(rent));
      formData.append('city', city);
      formData.append('barangay', barangay);
      formData.append('address', address);
      formData.append('property_type', propertyType);
      formData.append('bedrooms', String(beds));
      formData.append('bathrooms', String(baths));
      formData.append('amenities', JSON.stringify(amenities));
      if (location) {
        formData.append('latitude', String(location.latitude));
        formData.append('longitude', String(location.longitude));
      }

      if (image) {
        const localUri = image;
        const filename = localUri.split('/').pop() || 'photo.jpg';
        const match = /\.([a-zA-Z0-9]+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image`;

        formData.append('image', {
          uri: localUri,
          name: filename,
          type
        } as unknown as Blob);
      }

      await apiClient.post('/properties', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      Alert.alert('Success', 'Property listed successfully!');
      router.back();
    } catch (error: unknown) {
      const err = error as { response?: { data?: { message?: string } } };
      Alert.alert('Error', err.response?.data?.message || 'Failed to add property.');
    } finally {
      setLoading(false);
    }
  };


  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <Stack.Screen options={{ headerShown: false }} />
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>List a Property</Text>
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
            placeholder="e.g. Modern Apartment in Downtown"
            placeholderTextColor="#94a3b8"
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
            placeholder="e.g. 15000"
            placeholderTextColor="#94a3b8"
            keyboardType="numeric"
            value={monthlyRent}
            onChangeText={setMonthlyRent}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Street / Purok / Block</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            placeholder="e.g. Purok 1, Rosal Street"
            placeholderTextColor="#94a3b8"
            value={address}
            onChangeText={setAddress}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>Barangay</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            placeholder="e.g. Zone 1"
            placeholderTextColor="#94a3b8"
            value={barangay}
            onChangeText={setBarangay}
          />
        </View>

        <View style={styles.formGroup}>
          <Text style={[styles.label, isDarkMode && styles.textDark]}>City</Text>
          <TextInput
            style={[styles.input, isDarkMode && styles.inputDark]}
            placeholder="e.g. Digos City"
            placeholderTextColor="#94a3b8"
            value={city}
            onChangeText={setCity}
          />
        </View>

        <View style={styles.formGroup}>
          <View style={styles.mapHeaderRow}>
            <Text style={[styles.label, isDarkMode && styles.textDark]}>Pin Location on Map</Text>
            {geocodeStatus === 'searching' && <Text style={styles.statusSearching}>Searching...</Text>}
            {geocodeStatus === 'found' && <Text style={styles.statusFound}>✓ Found</Text>}
            {geocodeStatus === 'not_found' && <Text style={styles.statusNotFound}>Not Found (Pin Manually)</Text>}
          </View>
          <Text style={[styles.helpText, isDarkMode && styles.textMuted]}>Tap on the map or drag the pin to set the exact location.</Text>
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
            placeholder="Describe the property in detail"
            placeholderTextColor="#94a3b8"
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
              placeholder="e.g. 2"
              placeholderTextColor="#94a3b8"
              keyboardType="numeric"
              value={bedrooms}
              onChangeText={setBedrooms}
            />
          </View>
          <View style={[styles.formGroup, { flex: 1, marginLeft: 8 }]}>
            <Text style={[styles.label, isDarkMode && styles.textDark]}>Bathrooms</Text>
            <TextInput
              style={[styles.input, isDarkMode && styles.inputDark]}
              placeholder="e.g. 1"
              placeholderTextColor="#94a3b8"
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
          onPress={handleAddProperty}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitButtonText}>Publish Listing</Text>
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
