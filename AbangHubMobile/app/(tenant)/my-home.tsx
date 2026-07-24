import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, Image, TouchableOpacity, RefreshControl, Modal, TextInput, Alert, TouchableWithoutFeedback } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import { LinearGradient } from 'expo-linear-gradient';

export default function TenantMyHomeScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [data, setData] = useState<any>(null);

  // Review Modal State
  const [reviewModalVisible, setReviewModalVisible] = useState(false);
  const [rating, setRating] = useState(5);
  const [comment, setComment] = useState('');
  const [submittingReview, setSubmittingReview] = useState(false);

  // Hamburger Menu State
  const [menuVisible, setMenuVisible] = useState(false);

  const fetchData = async () => {
    try {
      const response = await apiClient.get('/tenant/dashboard');
      setData(response.data.data);
    } catch (error) {
      console.error('Error fetching tenant dashboard data', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchData();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchData();
  };

  const submitReview = async () => {
    if (!data?.active_lease?.property_id) return;
    
    try {
      setSubmittingReview(true);
      await apiClient.post('/reviews', {
        property_id: data.active_lease.property_id,
        rating,
        comment
      });
      Alert.alert('Success', 'Thank you for your review!');
      setReviewModalVisible(false);
      setComment('');
      setRating(5);
    } catch (error: any) {
      Alert.alert('Error', error.response?.data?.message || 'Failed to submit review.');
    } finally {
      setSubmittingReview(false);
    }
  };

  if (loading) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  // If no active lease
  if (!data?.active_lease) {
    return (
      <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
        <View style={styles.header}>
          <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>My Home</Text>
          <View style={styles.headerIcons}>
            <TouchableOpacity style={styles.iconBtn} onPress={() => router.push('/(tenant)/notifications' as any)}>
              <Ionicons name="notifications-outline" size={26} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
            </TouchableOpacity>
            <TouchableOpacity style={styles.iconBtn} onPress={() => setMenuVisible(true)}>
              <Ionicons name="menu" size={30} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
            </TouchableOpacity>
          </View>
        </View>
        <ScrollView contentContainerStyle={styles.emptyContent} refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} />}>
          <Ionicons name="home-outline" size={80} color={isDarkMode ? '#334155' : '#cbd5e1'} />
          <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Active Lease</Text>
          <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>You are not currently renting any property.</Text>
          <TouchableOpacity style={styles.exploreBtn} onPress={() => router.push('/(tenant)/explore' as any)}>
            <Text style={styles.exploreBtnText}>Explore Properties</Text>
          </TouchableOpacity>
        </ScrollView>
        {renderMenuModal()}
      </SafeAreaView>
    );
  }

  const lease = data.active_lease;
  const property = lease.property;
  const imageUrl = property.primary_image?.image_path || property.primaryImage?.image_path || 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1080&auto=format&fit=crop';
  const fullImageUrl = imageUrl.startsWith('http') ? imageUrl : `${apiClient.defaults.baseURL?.replace('/api', '')}${imageUrl}`;

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>My Home</Text>
        <View style={styles.headerIcons}>
          <TouchableOpacity style={styles.iconBtn} onPress={() => router.push('/(tenant)/notifications' as any)}>
            <Ionicons name="notifications-outline" size={26} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
          </TouchableOpacity>
          <TouchableOpacity style={styles.iconBtn} onPress={() => setMenuVisible(true)}>
            <Ionicons name="menu" size={30} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
          </TouchableOpacity>
        </View>
      </View>
      <ScrollView 
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" />}
      >
        <View style={[styles.propertyCard, isDarkMode && styles.cardDark]}>
          <Image source={{ uri: fullImageUrl }} style={styles.propertyImage} />
          <LinearGradient colors={['transparent', 'rgba(0,0,0,0.8)']} style={styles.imageOverlay}>
            <Text style={styles.propertyTitle}>{property.title}</Text>
            <Text style={styles.propertyAddress}><Ionicons name="location" size={14} /> {property.address}, {property.city}</Text>
          </LinearGradient>
        </View>

        <View style={[styles.detailsCard, isDarkMode && styles.cardDark]}>
          <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Lease Details</Text>
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, isDarkMode && styles.textMuted]}>Monthly Rent</Text>
            <Text style={[styles.detailValue, { color: '#e11d48' }]}>₱{parseFloat(lease.monthly_rent).toLocaleString()}</Text>
          </View>
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, isDarkMode && styles.textMuted]}>Start Date</Text>
            <Text style={[styles.detailValue, isDarkMode && styles.textDark]}>{lease.start_date}</Text>
          </View>
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, isDarkMode && styles.textMuted]}>End Date</Text>
            <Text style={[styles.detailValue, isDarkMode && styles.textDark]}>{lease.end_date}</Text>
          </View>
          
          <TouchableOpacity style={styles.landlordRow} onPress={() => router.push({ pathname: '/messages/[userId]', params: { userId: property.owner.id, propertyId: property.id } } as any)}>
            <View style={styles.landlordInfo}>
              <Ionicons name="person-circle-outline" size={36} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              <View style={styles.landlordText}>
                <Text style={[styles.landlordName, isDarkMode && styles.textDark]}>{property.owner.first_name} {property.owner.last_name}</Text>
                <Text style={[styles.landlordLabel, isDarkMode && styles.textMuted]}>Landlord</Text>
              </View>
            </View>
            <Ionicons name="chatbubble-ellipses" size={24} color="#e11d48" />
          </TouchableOpacity>
        </View>

        <View style={styles.actionGrid}>
          <TouchableOpacity style={[styles.actionBtn, isDarkMode && styles.actionBtnDark]} onPress={() => router.push('/(tenant)/invoices' as any)}>
            <View style={[styles.actionIconBg, { backgroundColor: '#10b98120' }]}>
              <Ionicons name="receipt-outline" size={24} color="#10b981" />
            </View>
            <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Rent Invoices</Text>
            {data.pending_invoices > 0 && (
              <View style={styles.badge}><Text style={styles.badgeText}>{data.pending_invoices}</Text></View>
            )}
          </TouchableOpacity>

          <TouchableOpacity style={[styles.actionBtn, isDarkMode && styles.actionBtnDark]} onPress={() => router.push('/(tenant)/maintenance' as any)}>
            <View style={[styles.actionIconBg, { backgroundColor: '#f9731620' }]}>
              <Ionicons name="hammer-outline" size={24} color="#f97316" />
            </View>
            <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Maintenance</Text>
          </TouchableOpacity>

          <TouchableOpacity style={[styles.actionBtn, isDarkMode && styles.actionBtnDark, { marginTop: 16 }]} onPress={() => setReviewModalVisible(true)}>
            <View style={[styles.actionIconBg, { backgroundColor: '#eab30820' }]}>
              <Ionicons name="star-outline" size={24} color="#eab308" />
            </View>
            <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Leave a Review</Text>
          </TouchableOpacity>
        </View>

      </ScrollView>

      {/* Review Modal */}
      <Modal visible={reviewModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, isDarkMode && styles.cardDark]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, isDarkMode && styles.textDark]}>Rate this Property</Text>
              <TouchableOpacity onPress={() => setReviewModalVisible(false)}>
                <Ionicons name="close" size={24} color={isDarkMode ? '#94a3b8' : '#64748b'} />
              </TouchableOpacity>
            </View>

            <View style={styles.starsContainer}>
              {[1, 2, 3, 4, 5].map((star) => (
                <TouchableOpacity key={star} onPress={() => setRating(star)}>
                  <Ionicons 
                    name={star <= rating ? "star" : "star-outline"} 
                    size={40} 
                    color={star <= rating ? "#eab308" : "#cbd5e1"} 
                  />
                </TouchableOpacity>
              ))}
            </View>

            <View style={styles.inputGroup}>
              <Text style={[styles.label, isDarkMode && styles.textMuted]}>Your Review (Optional)</Text>
              <TextInput
                style={[styles.input, styles.textArea, isDarkMode && styles.inputDark]}
                placeholder="How is your experience living here?"
                placeholderTextColor={isDarkMode ? '#64748b' : '#94a3b8'}
                value={comment}
                onChangeText={setComment}
                multiline
                numberOfLines={4}
                textAlignVertical="top"
              />
            </View>

            <TouchableOpacity 
              style={[styles.submitBtn, submittingReview && styles.submitBtnDisabled]} 
              onPress={submitReview}
              disabled={submittingReview}
            >
              {submittingReview ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.submitBtnText}>Submit Review</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
      {renderMenuModal()}
    </SafeAreaView>
  );

  function renderMenuModal() {
    return (
      <Modal visible={menuVisible} transparent animationType="fade">
        <TouchableWithoutFeedback onPress={() => setMenuVisible(false)}>
          <View style={styles.menuOverlay}>
            <TouchableWithoutFeedback>
              <View style={[styles.menuSheet, isDarkMode && styles.menuSheetDark]}>
                <View style={styles.menuSheetHeader}>
                  <Text style={[styles.menuSheetTitle, isDarkMode && styles.textDark]}>Quick Menu</Text>
                  <TouchableOpacity onPress={() => setMenuVisible(false)} style={styles.closeMenuBtn}>
                    <Ionicons name="close" size={24} color={isDarkMode ? '#94a3b8' : '#64748b'} />
                  </TouchableOpacity>
                </View>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/saved' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#e11d4820' }]}>
                    <Ionicons name="heart-outline" size={22} color="#e11d48" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>Saved Properties</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/applications' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#3b82f620' }]}>
                    <Ionicons name="document-text-outline" size={22} color="#3b82f6" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>My Applications</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/invoices' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#10b98120' }]}>
                    <Ionicons name="receipt-outline" size={22} color="#10b981" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>Rent Invoices</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/wallet' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#8b5cf620' }]}>
                    <Ionicons name="wallet-outline" size={22} color="#8b5cf6" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>My Wallet</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/maintenance' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#f59e0b20' }]}>
                    <Ionicons name="hammer-outline" size={22} color="#f59e0b" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>Maintenance</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>

                <TouchableOpacity style={styles.menuItem} onPress={() => { setMenuVisible(false); router.push('/(tenant)/profile' as any); }}>
                  <View style={[styles.menuIconBox, { backgroundColor: '#64748b20' }]}>
                    <Ionicons name="person-outline" size={22} color="#64748b" />
                  </View>
                  <Text style={[styles.menuItemText, isDarkMode && styles.textDark]}>Profile & Settings</Text>
                  <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#475569' : '#cbd5e1'} />
                </TouchableOpacity>
                
                <View style={styles.menuFooter}>
                  <Text style={styles.versionText}>AbangHub v1.0.0</Text>
                </View>
              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      </Modal>
    );
  }
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: 20, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#f1f5f9', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  headerTitle: { fontSize: 24, fontWeight: '700', color: '#0f172a' },
  headerIcons: { flexDirection: 'row', alignItems: 'center' },
  iconBtn: { marginLeft: 16 },
  scrollContent: { padding: 16, paddingBottom: 100 },
  propertyCard: { borderRadius: 16, overflow: 'hidden', marginBottom: 20, elevation: 4, shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.1, shadowRadius: 8 },
  propertyImage: { width: '100%', height: 200 },
  imageOverlay: { position: 'absolute', bottom: 0, left: 0, right: 0, padding: 20, paddingTop: 40 },
  propertyTitle: { color: '#fff', fontSize: 22, fontWeight: 'bold', marginBottom: 4 },
  propertyAddress: { color: 'rgba(255,255,255,0.8)', fontSize: 14 },
  cardDark: { backgroundColor: '#1e293b' },
  detailsCard: { backgroundColor: '#fff', borderRadius: 16, padding: 20, marginBottom: 20, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 6 },
  sectionTitle: { fontSize: 18, fontWeight: '700', color: '#0f172a', marginBottom: 16 },
  detailRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 },
  detailLabel: { fontSize: 15, color: '#64748b' },
  detailValue: { fontSize: 15, fontWeight: '600', color: '#0f172a' },
  landlordRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 16, paddingTop: 16, borderTopWidth: 1, borderTopColor: '#f1f5f9' },
  landlordInfo: { flexDirection: 'row', alignItems: 'center' },
  landlordText: { marginLeft: 12 },
  landlordName: { fontSize: 16, fontWeight: '600', color: '#0f172a' },
  landlordLabel: { fontSize: 13, color: '#64748b' },
  actionGrid: { flexDirection: 'row', justifyContent: 'space-between' },
  actionBtn: { flex: 1, backgroundColor: '#fff', borderRadius: 16, padding: 16, alignItems: 'center', marginHorizontal: 6, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 6 },
  actionBtnDark: { backgroundColor: '#1e293b' },
  actionIconBg: { padding: 16, borderRadius: 20, marginBottom: 12 },
  actionText: { fontSize: 14, fontWeight: '600', color: '#0f172a', textAlign: 'center' },
  badge: { position: 'absolute', top: 12, right: 12, backgroundColor: '#e11d48', borderRadius: 10, width: 20, height: 20, justifyContent: 'center', alignItems: 'center' },
  badgeText: { color: '#fff', fontSize: 10, fontWeight: 'bold' },
  emptyContent: { flexGrow: 1, justifyContent: 'center', alignItems: 'center', padding: 20 },
  emptyTitle: { fontSize: 20, fontWeight: '700', color: '#0f172a', marginTop: 16 },
  emptySubtitle: { fontSize: 15, color: '#64748b', textAlign: 'center', marginTop: 8, marginBottom: 24 },
  exploreBtn: { backgroundColor: '#e11d48', paddingHorizontal: 24, paddingVertical: 12, borderRadius: 24 },
  exploreBtnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', padding: 20 },
  modalContent: { backgroundColor: '#fff', borderRadius: 24, padding: 24 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 },
  modalTitle: { fontSize: 20, fontWeight: '700', color: '#0f172a' },
  starsContainer: { flexDirection: 'row', justifyContent: 'center', gap: 8, marginBottom: 24 },
  inputGroup: { marginBottom: 24 },
  label: { fontSize: 14, fontWeight: '500', color: '#475569', marginBottom: 8 },
  input: { backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 12, padding: 12, fontSize: 15, color: '#0f172a' },
  inputDark: { backgroundColor: '#0f172a', borderColor: '#334155', color: '#f8fafc' },
  textArea: { height: 100 },
  submitBtn: { backgroundColor: '#e11d48', padding: 16, borderRadius: 12, alignItems: 'center' },
  submitBtnDisabled: { opacity: 0.7 },
  submitBtnText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
  menuOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  menuSheet: { backgroundColor: '#ffffff', borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: 24, paddingBottom: 40 },
  menuSheetDark: { backgroundColor: '#1e293b' },
  menuSheetHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 },
  menuSheetTitle: { fontSize: 20, fontWeight: 'bold', color: '#0f172a' },
  closeMenuBtn: { padding: 4 },
  menuItem: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  menuIconBox: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center', marginRight: 16 },
  menuItemText: { flex: 1, fontSize: 16, fontWeight: '500', color: '#1e293b' },
  menuFooter: { marginTop: 32, alignItems: 'center' },
  versionText: { fontSize: 13, color: '#94a3b8' },
});
