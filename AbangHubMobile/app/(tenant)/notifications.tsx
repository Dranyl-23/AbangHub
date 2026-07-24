import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function TenantNotificationsScreen() {
  const { isDarkMode } = useTheme();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [notifications, setNotifications] = useState<any[]>([]);

  const fetchNotifications = async () => {
    try {
      const response = await apiClient.get('/notifications');
      setNotifications(response.data.data);
    } catch (error) {
      console.error('Error fetching notifications', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchNotifications();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchNotifications();
  };

  const renderItem = ({ item }: { item: any }) => (
    <View style={[styles.card, !item.is_read && styles.unreadCard, isDarkMode && styles.cardDark]}>
      <View style={styles.iconContainer}>
        <Ionicons name="notifications" size={24} color="#e11d48" />
      </View>
      <View style={styles.textContainer}>
        <Text style={[styles.title, isDarkMode && styles.textDark]}>{item.title}</Text>
        <Text style={[styles.message, isDarkMode && styles.textMuted]}>{item.message}</Text>
        <Text style={[styles.date, isDarkMode && styles.textMuted]}>{item.created_at}</Text>
      </View>
    </View>
  );

  if (loading && notifications.length === 0) {
    return (
      <View style={[styles.centerContainer, isDarkMode && styles.containerDark]}>
        <ActivityIndicator size="large" color="#e11d48" />
      </View>
    );
  }

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Notifications</Text>
      </View>

      <FlatList
        data={notifications}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={styles.listContent}
        refreshing={refreshing}
        onRefresh={handleRefresh}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="notifications-off-outline" size={60} color={isDarkMode ? '#334155' : '#cbd5e1'} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No Notifications</Text>
            <Text style={[styles.emptySubtitle, isDarkMode && styles.textMuted]}>You're all caught up!</Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { padding: 20, backgroundColor: '#ffffff', borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  headerTitle: { fontSize: 24, fontWeight: '700', color: '#0f172a' },
  listContent: { padding: 16 },
  card: { flexDirection: 'row', backgroundColor: '#fff', borderRadius: 16, padding: 16, marginBottom: 12, elevation: 1, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 3 },
  cardDark: { backgroundColor: '#1e293b' },
  unreadCard: { borderLeftWidth: 4, borderLeftColor: '#e11d48' },
  iconContainer: { marginRight: 16, justifyContent: 'center' },
  textContainer: { flex: 1 },
  title: { fontSize: 16, fontWeight: '600', color: '#0f172a', marginBottom: 4 },
  message: { fontSize: 14, color: '#475569', marginBottom: 8 },
  date: { fontSize: 12, color: '#94a3b8' },
  emptyContainer: { alignItems: 'center', marginTop: 80 },
  emptyTitle: { fontSize: 18, fontWeight: '600', color: '#0f172a', marginTop: 16 },
  emptySubtitle: { fontSize: 14, color: '#64748b', marginTop: 8 },
  textDark: { color: '#f8fafc' },
  textMuted: { color: '#94a3b8' },
});
