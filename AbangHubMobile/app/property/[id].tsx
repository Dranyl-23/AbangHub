import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, ScrollView, Image, TouchableOpacity, SafeAreaView, Dimensions, Modal, TextInput, Alert, Platform } from 'react-native';
import { useLocalSearchParams, router, Stack } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import MapView, { Marker } from 'react-native-maps';
import DateTimePicker, { DateTimePickerEvent } from '@react-native-community/datetimepicker';
import apiClient from '../../src/api/client';
import { Property } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';

const { width } = Dimensions.get('window');

export default function PropertyDetails() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const { isDarkMode } = useTheme();
  
  // Helper to ensure image URLs point to the correct backend IP instead of localhost
  const getValidImageUrl = (imagePath: string | null | undefined) => {
    if (!imagePath) return null;
    if (imagePath.startsWith('http://192.') || imagePath.startsWith('https://')) return imagePath;
    
    const baseUrl = apiClient.defaults.baseURL?.replace('/api', '') || '';
    
    if (imagePath.startsWith('http://localhost') || imagePath.startsWith('http://127.0.0.1')) {
      try {
        const url = new URL(imagePath);
        return `${baseUrl}${url.pathname}`;
      } catch (e) {
        return imagePath;
      }
    }
    
    if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
      return `${baseUrl}/storage/${imagePath}`;
    }
    
    if (imagePath.startsWith('/')) {
      return `${baseUrl}${imagePath}`;
    }
    
    return imagePath;
  };
  const [property, setProperty] = useState<Property | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  
  // Application Modal States
  const [modalVisible, setModalVisible] = useState(false);
  const [moveInDate, setMoveInDate] = useState<Date>(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)); // Default: 1 week from now
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [occupants, setOccupants] = useState('1');
  const [occupantsError, setOccupantsError] = useState<string | null>(null);
  const [message, setMessage] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const formatDate = (date: Date) =>
    date.toISOString().split('T')[0]; // → 'YYYY-MM-DD'

  const handleDateChange = (_: DateTimePickerEvent, selected?: Date) => {
    setShowDatePicker(Platform.OS === 'ios'); // Keep open on iOS
    if (selected) setMoveInDate(selected);
  };

  const validateOccupants = (value: string): boolean => {
    const num = parseInt(value, 10);
    if (!value || isNaN(num)) {
      setOccupantsError('Please enter a valid number');
      return false;
    }
    if (num < 1) {
      setOccupantsError('Minimum 1 occupant');
      return false;
    }
    if (num > 20) {
      setOccupantsError('Maximum 20 occupants');
      return false;
    }
    setOccupantsError(null);
    return true;
  };

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

  const handleApply = async () => {
    if (!message.trim()) {
      Alert.alert('Error', 'Please write a message to the landlord');
      return;
    }
    if (!validateOccupants(occupants)) return;

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (moveInDate <= today) {
      Alert.alert('Error', 'Move-in date must be in the future');
      return;
    }

    setSubmitting(true);
    try {
      await apiClient.post(`/properties/${id}/apply`, {
        move_in_date: formatDate(moveInDate),
        occupants: parseInt(occupants, 10),
        message: message.trim(),
      });
      Alert.alert('Success', 'Application submitted successfully!');
      setModalVisible(false);
      router.push('/applications' as never);
    } catch (error: unknown) {
      const err = error as { response?: { data?: { message?: string } } };
      Alert.alert('Error', err.response?.data?.message || 'Failed to submit application');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  if (!property) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
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

  const fullImageUrl = primaryImage.startsWith('http') ? primaryImage : `${apiClient.defaults.baseURL?.replace('/api', '')}${primaryImage}`;

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]}>
      <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>
        
        {/* Header Image */}
        <View style={styles.imageContainer}>
          <Image source={{ uri: fullImageUrl }} style={styles.mainImage} />
          <TouchableOpacity style={styles.backIconBtn} onPress={() => router.back()}>
            <Ionicons name="arrow-back" size={24} color="#0f172a" />
          </TouchableOpacity>
        </View>

        {/* Content */}
        <View style={[styles.contentContainer, isDarkMode && styles.contentContainerDark]}>
          <View style={styles.titleRow}>
            <Text style={[styles.title, isDarkMode && styles.textDark]}>{property.title}</Text>
          </View>
          
          <Text style={styles.price}>₱{Number(property.monthly_rent).toLocaleString()} <Text style={styles.priceUnit}>/ month</Text></Text>
          
          <View style={styles.locationRow}>
            <Ionicons name="location" size={16} color="#64748b" />
            <Text style={styles.locationText}>{property.address}, {property.city}, {property.province}</Text>
          </View>

          {/* Quick Stats */}
          <View style={styles.statsContainer}>
            <View style={[styles.statBox, isDarkMode && styles.statBoxDark]}>
              <Ionicons name="home" size={20} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
              <Text style={[styles.statText, isDarkMode && styles.textDark]}>{property.property_type.replace('_', ' ').toUpperCase()}</Text>
            </View>
            <View style={[styles.statBox, isDarkMode && styles.statBoxDark]}>
              <Ionicons name="bed" size={20} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
              <Text style={[styles.statText, isDarkMode && styles.textDark]}>{property.bedrooms} Bed</Text>
            </View>
            <View style={[styles.statBox, isDarkMode && styles.statBoxDark]}>
              <Ionicons name="water" size={20} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
              <Text style={[styles.statText, isDarkMode && styles.textDark]}>{property.bathrooms} Bath</Text>
            </View>
          </View>

          {/* Divider */}
          <View style={[styles.divider, isDarkMode && styles.dividerDark]} />

          {/* Description */}
          <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Description</Text>
          <Text style={[styles.description, isDarkMode && styles.descriptionDark]}>{property.description || 'No description available for this property.'}</Text>

          {/* Divider */}
          <View style={[styles.divider, isDarkMode && styles.dividerDark]} />

          {/* Amenities */}
          {property.amenities && property.amenities.length > 0 && (
            <>
              <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Amenities</Text>
              <View style={styles.amenitiesContainer}>
                {property.amenities.map((amenity: any, index: number) => (
                  <View key={index} style={[styles.amenityChip, isDarkMode && styles.amenityChipDark]}>
                    <Ionicons name="checkmark-circle" size={16} color="#e11d48" />
                    <Text style={[styles.amenityText, isDarkMode && styles.amenityTextDark]}>{amenity.amenity_name}</Text>
                  </View>
                ))}
              </View>
              <View style={[styles.divider, isDarkMode && styles.dividerDark]} />
            </>
          )}

          {/* Location / Map */}
          <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Location</Text>
          <View style={styles.mapContainer}>
            <MapView
              style={styles.map}
              initialRegion={{
                latitude: Number(property.latitude) || 6.7483, // Digos City center fallback
                longitude: Number(property.longitude) || 125.3560,
                latitudeDelta: 0.003,
                longitudeDelta: 0.003,
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
          <View style={[styles.divider, isDarkMode && styles.dividerDark]} />

          {/* Owner Info */}
          <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Listed By</Text>
          <View style={[styles.ownerBox, isDarkMode && styles.ownerBoxDark]}>
            <View style={styles.ownerAvatar}>
              {property.owner?.avatar_url || property.owner?.profile_image ? (
                <Image 
                  source={{ uri: getValidImageUrl(property.owner?.avatar_url) || getValidImageUrl(property.owner?.profile_image) || undefined }} 
                  style={{ width: '100%', height: '100%', borderRadius: 24 }} 
                />
              ) : (
                <Text style={styles.ownerInitial}>{(property.owner?.full_name?.[0] || property.owner?.username?.[0] || 'L').toUpperCase()}</Text>
              )}
            </View>
            <View style={styles.ownerInfo}>
              <Text style={[styles.ownerName, isDarkMode && styles.textDark]}>{property.owner?.full_name || property.owner?.username || 'Landlord'}</Text>
              <Text style={styles.ownerRole}>Property Owner</Text>
            </View>
            <TouchableOpacity 
              style={[styles.contactBtn, isDarkMode && styles.contactBtnDark]}
              onPress={() => router.push(`/messages/${property.owner_id}?propertyId=${property.id}` as any)}
            >
              <Ionicons name="chatbubble-ellipses" size={20} color="#e11d48" />
            </TouchableOpacity>
          </View>

          {/* Divider */}
          <View style={[styles.divider, isDarkMode && styles.dividerDark]} />

          {/* Reviews Section */}
          <View style={styles.reviewsHeader}>
            <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>
              <Ionicons name="star" size={20} color="#eab308" /> {property.average_rating ? property.average_rating.toFixed(1) : '0.0'} · {property.review_count || 0} reviews
            </Text>
          </View>
          
          <View style={styles.reviewsList}>
            {(!property.reviews || property.reviews.length === 0) ? (
              <Text style={[styles.noReviewsText, isDarkMode && styles.textMuted]}>No reviews yet.</Text>
            ) : (
              property.reviews.map((review) => (
                <View key={review.id} style={[styles.reviewCard, isDarkMode && styles.reviewCardDark]}>
                  <View style={styles.reviewHeader}>
                    <View style={styles.reviewAvatar}>
                      {review.tenant?.avatar_url || review.tenant?.profile_image ? (
                        <Image 
                          source={{ uri: getValidImageUrl(review.tenant?.avatar_url) || getValidImageUrl(review.tenant?.profile_image) || undefined }} 
                          style={styles.reviewAvatarImg} 
                        />
                      ) : (
                        <Text style={styles.reviewInitial}>{(review.tenant?.full_name?.[0] || review.tenant?.username?.[0] || 'T').toUpperCase()}</Text>
                      )}
                    </View>
                    <View style={styles.reviewAuthorInfo}>
                      <Text style={[styles.reviewAuthorName, isDarkMode && styles.textDark]}>{review.tenant?.full_name || review.tenant?.username || 'Tenant'}</Text>
                      <Text style={[styles.reviewDate, isDarkMode && styles.textMuted]}>
                        {new Date(review.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short' })}
                      </Text>
                    </View>
                  </View>
                  <View style={styles.starsContainer}>
                    {[1, 2, 3, 4, 5].map((star) => (
                      <Ionicons 
                        key={star} 
                        name={star <= review.rating ? "star" : "star-outline"} 
                        size={14} 
                        color={star <= review.rating ? "#eab308" : "#cbd5e1"} 
                      />
                    ))}
                  </View>
                  {review.comment ? (
                    <Text style={[styles.reviewComment, isDarkMode && styles.textDark]}>{review.comment}</Text>
                  ) : null}
                </View>
              ))
            )}
          </View>
        </View>
      </ScrollView>

      {/* Sticky Bottom Bar */}
      <View style={[styles.bottomBar, isDarkMode && styles.bottomBarDark]}>
        <View style={styles.bottomPrice}>
          <Text style={styles.bottomPriceLabel}>Total Price</Text>
          <Text style={[styles.bottomPriceValue, isDarkMode && styles.textDark]}>₱{Number(property.monthly_rent).toLocaleString()}</Text>
        </View>
        <TouchableOpacity style={styles.rentButton} onPress={() => setModalVisible(true)}>
          <Text style={styles.rentButtonText}>Rent Now</Text>
        </TouchableOpacity>
      </View>

      {/* Application Modal */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.modalContentDark]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Apply for Property</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
              </TouchableOpacity>
            </View>
            
            {/* Move-in Date Picker */}
            <Text style={[styles.inputLabel, isDarkMode && styles.inputLabelDark]}>Move-in Date</Text>
            <TouchableOpacity
              style={[styles.input, isDarkMode && styles.inputDark, { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }]}
              onPress={() => setShowDatePicker(true)}
            >
              <Text style={{ color: isDarkMode ? '#f8fafc' : '#0f172a', fontSize: 16 }}>
                {formatDate(moveInDate)}
              </Text>
              <Ionicons name="calendar-outline" size={20} color="#e11d48" />
            </TouchableOpacity>
            {showDatePicker && (
              <DateTimePicker
                value={moveInDate}
                mode="date"
                display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                minimumDate={new Date(Date.now() + 24 * 60 * 60 * 1000)}
                onChange={handleDateChange}
              />
            )}

            {/* Occupants with Validation */}
            <Text style={[styles.inputLabel, isDarkMode && styles.inputLabelDark]}>Number of Occupants</Text>
            <TextInput 
              style={[styles.input, isDarkMode && styles.inputDark, occupantsError ? { borderColor: '#e11d48' } : {}]} 
              placeholder="e.g. 2" 
              placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
              keyboardType="numeric"
              value={occupants}
              onChangeText={(val) => {
                setOccupants(val);
                if (val) validateOccupants(val);
              }}
            />
            {occupantsError && (
              <Text style={{ color: '#e11d48', fontSize: 12, marginTop: 4 }}>{occupantsError}</Text>
            )}

            <Text style={[styles.inputLabel, isDarkMode && styles.inputLabelDark]}>Message to Landlord</Text>
            <TextInput 
              style={[styles.input, styles.textArea, isDarkMode && styles.inputDark]} 
              placeholder="Hi, I am interested in renting..." 
              placeholderTextColor={isDarkMode ? "#64748b" : "#94a3b8"}
              multiline={true}
              numberOfLines={4}
              value={message}
              onChangeText={setMessage}
            />

            <TouchableOpacity 
              style={styles.submitModalBtn} 
              onPress={handleApply}
              disabled={submitting}
            >
              {submitting ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.submitModalBtnText}>Submit Application</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

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
  containerDark: {
    backgroundColor: '#0f172a',
  },
  textDark: {
    color: '#f8fafc',
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
  contentContainerDark: {
    backgroundColor: '#0f172a',
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
  statBoxDark: {
    backgroundColor: '#1e293b',
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
  dividerDark: {
    backgroundColor: '#334155',
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
  descriptionDark: {
    color: '#94a3b8',
  },
  amenitiesContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 8,
  },
  amenityChip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 20,
    gap: 6,
  },
  amenityChipDark: {
    backgroundColor: '#334155',
  },
  amenityText: {
    fontSize: 14,
    color: '#475569',
    fontWeight: '500',
  },
  amenityTextDark: {
    color: '#cbd5e1',
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
  ownerBoxDark: {
    backgroundColor: '#1e293b',
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
  contactBtnDark: {
    backgroundColor: 'rgba(225, 29, 72, 0.2)',
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
  bottomBarDark: {
    backgroundColor: '#0f172a',
    borderTopColor: '#334155',
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
  modalOverlay: {
    flex: 1,
    backgroundColor: 'transparent',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 24,
    minHeight: '60%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.1,
    shadowRadius: 12,
    elevation: 20,
  },
  modalContentDark: {
    backgroundColor: '#1e293b',
    shadowColor: '#000',
    shadowOpacity: 0.3,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#0f172a',
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#334155',
    marginBottom: 8,
    marginTop: 12,
  },
  inputLabelDark: {
    color: '#94a3b8',
  },
  input: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    padding: 14,
    fontSize: 16,
    color: '#0f172a',
  },
  inputDark: {
    backgroundColor: '#0f172a',
    borderColor: '#334155',
    color: '#f8fafc',
  },
  textArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  submitModalBtn: {
    backgroundColor: '#e11d48',
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 24,
  },
  submitModalBtnText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  reviewsHeader: {
    marginBottom: 16,
  },
  reviewsList: {
    gap: 16,
    paddingBottom: 24,
  },
  noReviewsText: {
    fontSize: 15,
    color: '#64748b',
    fontStyle: 'italic',
    textAlign: 'center',
    paddingVertical: 16,
  },
  reviewCard: {
    backgroundColor: '#ffffff',
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  reviewCardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  reviewHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  reviewAvatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#e2e8f0',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
    overflow: 'hidden',
  },
  reviewAvatarImg: {
    width: '100%',
    height: '100%',
  },
  reviewInitial: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#64748b',
  },
  reviewAuthorInfo: {
    flex: 1,
  },
  reviewAuthorName: {
    fontSize: 15,
    fontWeight: '700',
    color: '#0f172a',
  },
  reviewDate: {
    fontSize: 12,
    color: '#64748b',
    marginTop: 2,
  },
  starsContainer: {
    flexDirection: 'row',
    marginBottom: 8,
    gap: 2,
  },
  reviewComment: {
    fontSize: 14,
    color: '#334155',
    lineHeight: 20,
  },
});
