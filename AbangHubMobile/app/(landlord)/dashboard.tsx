import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, TouchableOpacity, RefreshControl, Dimensions, Modal, TouchableWithoutFeedback } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';
import { LinearGradient } from 'expo-linear-gradient';
import * as SecureStore from 'expo-secure-store';const { width } = Dimensions.get('window');

export default function LandlordDashboardScreen() {
  const { isDarkMode } = useTheme();
  const [userName, setUserName] = useState<string>('Landlord');
  const [metrics, setMetrics] = useState({
    available_properties: 0,
    total_income_this_month: 0,
    total_expenses_this_month: 0,
    net_income: 0,
    pending_applications: 0,
    occupancy_rate: 0,
    expiring_leases: [] as any[]
  });
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [menuVisible, setMenuVisible] = useState(false);

  const fetchDashboardData = async () => {
    try {
      const userDataStr = await SecureStore.getItemAsync('userData');
      if (userDataStr) {
        const user = JSON.parse(userDataStr);
        setUserName(user.full_name || user.username || 'Landlord');
      }

      const response = await apiClient.get('/landlord/dashboard');
      setMetrics(response.data.data);
    } catch (error) {
      console.error('Error fetching dashboard data', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchDashboardData();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchDashboardData();
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <ScrollView 
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" colors={['#e11d48']} />
        }
      >
        <View style={styles.header}>
          <View style={{ flexShrink: 1, marginRight: 12 }}>
            <Text style={[styles.greeting, isDarkMode && styles.textDark]}>Hello, {userName}!</Text>
            <Text style={[styles.dateText, isDarkMode && styles.textMuted]}>Here's your monthly summary</Text>
          </View>
          <View style={{ flexDirection: 'row', alignItems: 'center' }}>
            <TouchableOpacity style={[styles.hamburgerButton, { marginRight: 8 }]} onPress={() => router.push('/(landlord)/notifications' as any)}>
              <Ionicons name="notifications-outline" size={28} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
              {/* Optional: Add a red dot badge here if unread notifications > 0 */}
            </TouchableOpacity>
            <TouchableOpacity style={styles.hamburgerButton} onPress={() => setMenuVisible(true)}>
              <Ionicons name="menu" size={32} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
            </TouchableOpacity>
          </View>
        </View>

        {loading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color="#e11d48" />
          </View>
        ) : (
          <View style={styles.metricsContainer}>
            
            {/* Income Card (Full Width) */}
            <TouchableOpacity activeOpacity={0.9} onPress={() => router.push('/(landlord)/invoices' as any)}>
              <LinearGradient
                colors={['#f43f5e', '#e11d48']}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 1 }}
                style={styles.mainCard}
              >
                <View style={styles.cardHeader}>
                  <Text style={styles.cardTitleLight}>Net Income</Text>
                  <Ionicons name="wallet-outline" size={24} color="rgba(255,255,255,0.8)" />
                </View>
                <Text style={styles.cardValueLight}>
                  ₱{metrics.net_income.toLocaleString()}
                </Text>
                
                <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginTop: 12, borderTopWidth: 1, borderTopColor: 'rgba(255,255,255,0.2)', paddingTop: 12 }}>
                  <View>
                    <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 12 }}>Total Income</Text>
                    <Text style={{ color: '#fff', fontSize: 14, fontWeight: 'bold' }}>₱{metrics.total_income_this_month.toLocaleString()}</Text>
                  </View>
                  <View style={{ alignItems: 'flex-end' }}>
                    <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 12 }}>Expenses</Text>
                    <Text style={{ color: '#fff', fontSize: 14, fontWeight: 'bold' }}>₱{metrics.total_expenses_this_month.toLocaleString()}</Text>
                  </View>
                </View>
              </LinearGradient>
            </TouchableOpacity>

            {/* Renewing Soon Alerts */}
            {metrics.expiring_leases && metrics.expiring_leases.length > 0 && (
              <View style={[styles.alertContainer, isDarkMode && styles.alertContainerDark]}>
                <View style={styles.alertHeader}>
                  <Ionicons name="notifications-outline" size={20} color="#f59e0b" />
                  <Text style={[styles.alertTitle, isDarkMode && styles.textDark]}>Renewing Soon</Text>
                </View>
                {metrics.expiring_leases.map((lease: any, index: number) => (
                  <View key={lease.id} style={[styles.alertItem, isDarkMode && styles.alertItemDark, index === metrics.expiring_leases.length - 1 && { borderBottomWidth: 0 }]}>
                    <View style={styles.alertItemLeft}>
                      <Text style={[styles.alertTenantName, isDarkMode && styles.textDark]}>{lease.tenant_name}</Text>
                      <Text style={[styles.alertPropertyTitle, isDarkMode && styles.textMuted]}>{lease.property_title} • {lease.days_left} days left</Text>
                    </View>
                    <TouchableOpacity 
                      style={styles.remindButton} 
                      onPress={() => router.push(`/messages/${lease.tenant_id}?propertyId=${lease.property_id}` as any)}
                    >
                      <Text style={styles.remindButtonText}>Remind</Text>
                    </TouchableOpacity>
                  </View>
                ))}
              </View>
            )}

            {/* Two Column Cards */}
            <View style={styles.rowCards}>
              
              <TouchableOpacity 
                activeOpacity={0.9} 
                style={[styles.smallCard, isDarkMode && styles.smallCardDark]}
                onPress={() => router.push('/(landlord)/properties' as any)}
              >
                <View style={[styles.iconContainer, { backgroundColor: '#3b82f620' }]}>
                  <Ionicons name="home-outline" size={24} color="#3b82f6" />
                </View>
                <Text style={[styles.smallCardValue, isDarkMode && styles.textDark]}>
                  {metrics.available_properties}
                </Text>
                <Text style={[styles.smallCardTitle, isDarkMode && styles.textMuted]}>
                  Available{'\n'}Properties
                </Text>
                <Text style={{ fontSize: 12, color: '#10b981', marginTop: 4, fontWeight: '600' }}>
                  {metrics.occupancy_rate}% Occupied
                </Text>
              </TouchableOpacity>

              <TouchableOpacity 
                activeOpacity={0.9} 
                style={[styles.smallCard, isDarkMode && styles.smallCardDark]}
                onPress={() => router.push('/(landlord)/applications' as any)}
              >
                <View style={[styles.iconContainer, { backgroundColor: '#f59e0b20' }]}>
                  <Ionicons name="document-text-outline" size={24} color="#f59e0b" />
                </View>
                <Text style={[styles.smallCardValue, isDarkMode && styles.textDark]}>
                  {metrics.pending_applications}
                </Text>
                <Text style={[styles.smallCardTitle, isDarkMode && styles.textMuted]}>
                  Pending{'\n'}Applications
                </Text>
              </TouchableOpacity>

            </View>

            {/* Quick Actions */}
            <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Quick Actions</Text>
            
            <View style={[styles.actionList, isDarkMode && styles.actionListDark]}>
              <TouchableOpacity style={styles.actionItem} onPress={() => router.push('/(landlord)/add-property' as any)}>
                <View style={[styles.actionIconBg, { backgroundColor: '#10b98120' }]}>
                  <Ionicons name="add-circle-outline" size={24} color="#10b981" />
                </View>
                <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Add New Property</Text>
                <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#64748b' : '#94a3b8'} />
              </TouchableOpacity>

              <TouchableOpacity style={styles.actionItem} onPress={() => router.push('/(landlord)/tenants' as any)}>
                <View style={[styles.actionIconBg, { backgroundColor: '#8b5cf620' }]}>
                  <Ionicons name="people-outline" size={24} color="#8b5cf6" />
                </View>
                <Text style={[styles.actionText, isDarkMode && styles.textDark]}>View Active Tenants</Text>
                <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#64748b' : '#94a3b8'} />
              </TouchableOpacity>

              <TouchableOpacity style={styles.actionItem} onPress={() => router.push('/(landlord)/maintenance' as any)}>
                <View style={[styles.actionIconBg, { backgroundColor: '#f9731620' }]}>
                  <Ionicons name="hammer-outline" size={24} color="#f97316" />
                </View>
                <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Maintenance Requests</Text>
                <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#64748b' : '#94a3b8'} />
              </TouchableOpacity>

              <TouchableOpacity style={[styles.actionItem, { borderBottomWidth: 0 }]} onPress={() => router.push('/(landlord)/expenses' as any)}>
                <View style={[styles.actionIconBg, { backgroundColor: '#e11d4820' }]}>
                  <Ionicons name="cash-outline" size={24} color="#e11d48" />
                </View>
                <Text style={[styles.actionText, isDarkMode && styles.textDark]}>Expense Tracker</Text>
                <Ionicons name="chevron-forward" size={20} color={isDarkMode ? '#64748b' : '#94a3b8'} />
              </TouchableOpacity>
            </View>

          </View>
        )}
      </ScrollView>

      {/* Hamburger Dropdown Menu */}
      <Modal visible={menuVisible} transparent={true} animationType="fade" onRequestClose={() => setMenuVisible(false)}>
        <TouchableWithoutFeedback onPress={() => setMenuVisible(false)}>
          <View style={styles.modalOverlay}>
            <View style={[styles.dropdownMenu, isDarkMode && styles.dropdownMenuDark]}>
              <TouchableOpacity 
                style={styles.dropdownItem} 
                onPress={() => {
                  setMenuVisible(false);
                  router.push('/(landlord)/applications' as any);
                }}
              >
                <Ionicons name="document-text-outline" size={20} color={isDarkMode ? '#e2e8f0' : '#475569'} />
                <Text style={[styles.dropdownItemText, isDarkMode && styles.textDark]}>Applications</Text>
              </TouchableOpacity>

              <TouchableOpacity 
                style={styles.dropdownItem} 
                onPress={() => {
                  setMenuVisible(false);
                  router.push('/(landlord)/tenants' as any);
                }}
              >
                <Ionicons name="people-outline" size={20} color={isDarkMode ? '#e2e8f0' : '#475569'} />
                <Text style={[styles.dropdownItemText, isDarkMode && styles.textDark]}>Active Tenants</Text>
              </TouchableOpacity>
              
              <TouchableOpacity 
                style={styles.dropdownItem} 
                onPress={() => {
                  setMenuVisible(false);
                  router.push('/(landlord)/invoices' as any);
                }}
              >
                <Ionicons name="receipt-outline" size={20} color={isDarkMode ? '#e2e8f0' : '#475569'} />
                <Text style={[styles.dropdownItemText, isDarkMode && styles.textDark]}>Invoices & Payments</Text>
              </TouchableOpacity>

              <TouchableOpacity 
                style={styles.dropdownItem} 
                onPress={() => {
                  setMenuVisible(false);
                  router.push('/(landlord)/expenses' as any);
                }}
              >
                <Ionicons name="cash-outline" size={20} color={isDarkMode ? '#e2e8f0' : '#475569'} />
                <Text style={[styles.dropdownItemText, isDarkMode && styles.textDark]}>Expense Tracker</Text>
              </TouchableOpacity>

              <TouchableOpacity 
                style={styles.dropdownItem} 
                onPress={() => {
                  setMenuVisible(false);
                  router.push('/(landlord)/maintenance' as any);
                }}
              >
                <Ionicons name="hammer-outline" size={20} color={isDarkMode ? '#e2e8f0' : '#475569'} />
                <Text style={[styles.dropdownItemText, isDarkMode && styles.textDark]}>Maintenance Requests</Text>
              </TouchableOpacity>
            </View>
          </View>
        </TouchableWithoutFeedback>
      </Modal>
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
  scrollContent: {
    paddingBottom: 40,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 15,
  },
  hamburgerButton: {
    padding: 8,
    marginTop: 2,
  },
  greeting: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#0f172a',
    letterSpacing: -0.5,
  },
  dateText: {
    fontSize: 16,
    color: '#64748b',
    marginTop: 4,
  },
  loadingContainer: {
    paddingTop: 40,
    justifyContent: 'center',
    alignItems: 'center',
  },
  metricsContainer: {
    paddingHorizontal: 20,
  },
  mainCard: {
    padding: 16,
    marginBottom: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 12,
    elevation: 8,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  cardTitleLight: {
    fontSize: 16,
    fontWeight: '600',
    color: 'rgba(255,255,255,0.9)',
  },
  cardValueLight: {
    fontSize: 36,
    fontWeight: 'bold',
    color: '#ffffff',
    marginBottom: 8,
    letterSpacing: -1,
  },
  cardSubTextLight: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.7)',
  },
  rowCards: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  smallCard: {
    width: (width - 56) / 2, // Half width minus padding/gap
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#f1f5f9',
  },
  smallCardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  iconContainer: {
    width: 48,
    height: 48,
    borderRadius: 14,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  smallCardValue: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 4,
  },
  smallCardTitle: {
    fontSize: 14,
    color: '#64748b',
    fontWeight: '500',
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#0f172a',
    marginBottom: 16,
  },
  actionList: {
    backgroundColor: '#ffffff',
    borderRadius: 20,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#f1f5f9',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.03,
    shadowRadius: 8,
    elevation: 1,
  },
  actionListDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  actionItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  actionIconBg: {
    width: 40,
    height: 40,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  actionText: {
    flex: 1,
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
  },
  textDark: {
    color: '#f8fafc',
  },
  textMuted: {
    color: '#94a3b8',
  },
  alertContainer: {
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 16,
    marginBottom: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#f59e0b30',
  },
  alertContainerDark: {
    backgroundColor: '#1e293b',
    borderColor: '#f59e0b20',
  },
  alertHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  alertTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#0f172a',
    marginLeft: 8,
  },
  alertItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
  },
  alertItemDark: {
    borderBottomColor: '#334155',
  },
  alertItemLeft: {
    flex: 1,
    marginRight: 12,
  },
  alertTenantName: {
    fontSize: 15,
    fontWeight: '600',
    color: '#0f172a',
    marginBottom: 4,
  },
  alertPropertyTitle: {
    fontSize: 13,
    color: '#64748b',
  },
  remindButton: {
    backgroundColor: 'rgba(245, 158, 11, 0.1)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  remindButtonText: {
    color: '#f59e0b',
    fontWeight: '600',
    fontSize: 13,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.1)',
  },
  dropdownMenu: {
    position: 'absolute',
    top: 70,
    right: 20,
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 8,
    width: 220,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
    elevation: 10,
  },
  dropdownMenuDark: {
    backgroundColor: '#1e293b',
    shadowColor: '#000',
  },
  dropdownItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 12,
    borderRadius: 8,
  },
  dropdownItemText: {
    fontSize: 16,
    color: '#0f172a',
    marginLeft: 12,
    fontWeight: '500',
  },
});
