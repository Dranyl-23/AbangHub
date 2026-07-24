import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, ActivityIndicator, TouchableOpacity, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { router, useFocusEffect } from 'expo-router';
import apiClient from '../../src/api/client';
import { useTheme } from '../../src/context/ThemeContext';

export default function NotificationsScreen() {
  const { isDarkMode } = useTheme();
  const [notifications, setNotifications] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

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

  const markAsRead = async (id: string) => {
    try {
      await apiClient.put(`/notifications/${id}/read`);
      setNotifications(prev => prev.map(n => n.id === id ? { ...n, read_at: new Date().toISOString() } : n));
    } catch (error) {
      console.error('Error marking as read', error);
    }
  };

  const markAllAsRead = async () => {
    try {
      await apiClient.put('/notifications/mark-all-read');
      fetchNotifications();
    } catch (error) {
      console.error('Error marking all as read', error);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    fetchNotifications();
  };

  const renderNotification = ({ item }: { item: any }) => {
    const isUnread = !item.read_at;
    
    return (
      <TouchableOpacity 
        style={[styles.notificationCard, isDarkMode && styles.notificationCardDark, isUnread && (isDarkMode ? styles.unreadCardDark : styles.unreadCard)]}
        onPress={() => isUnread && markAsRead(item.id)}
      >
        <View style={styles.iconContainer}>
          <Ionicons 
            name={item.type.includes('Application') ? 'document-text' : item.type.includes('Maintenance') ? 'hammer' : 'notifications'} 
            size={24} 
            color="#e11d48" 
          />
        </View>
        <View style={styles.textContainer}>
          <Text style={[styles.title, isDarkMode && styles.textDark]}>{item.data?.title || 'New Notification'}</Text>
          <Text style={[styles.message, isDarkMode && styles.textMuted]}>{item.data?.message || 'You have a new update.'}</Text>
          <Text style={styles.time}>{new Date(item.created_at).toLocaleString()}</Text>
        </View>
        {isUnread && <View style={styles.unreadDot} />}
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
          <TouchableOpacity onPress={() => router.back()} style={{ marginRight: 16 }}>
            <Ionicons name="arrow-back" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
          </TouchableOpacity>
          <View>
            <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Notifications</Text>
            <Text style={[styles.headerSubtitle, isDarkMode && styles.textMuted]}>Stay updated with your properties</Text>
          </View>
        </View>
        <TouchableOpacity onPress={markAllAsRead}>
          <Ionicons name="checkmark-done-outline" size={28} color="#e11d48" />
        </TouchableOpacity>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : notifications.length === 0 ? (
        <View style={styles.centerContainer}>
          <Ionicons name="notifications-off-outline" size={64} color={isDarkMode ? '#334155' : '#cbd5e1'} />
          <Text style={[styles.emptyText, isDarkMode && styles.textMuted]}>No notifications yet.</Text>
        </View>
      ) : (
        <FlatList
          data={notifications}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderNotification}
          contentContainerStyle={styles.listContent}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor="#e11d48" />}
        />
      )}
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
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 24,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#e2e8f0',
  },
  headerDark: {
    backgroundColor: '#1e293b',
    borderBottomColor: '#334155',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: '700',
    color: '#0f172a',
  },
  headerSubtitle: {
    fontSize: 14,
    color: '#64748b',
    marginTop: 4,
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
  emptyText: {
    fontSize: 16,
    color: '#64748b',
    marginTop: 16,
  },
  listContent: {
    padding: 16,
  },
  notificationCard: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 16,
    marginBottom: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  notificationCardDark: {
    backgroundColor: '#1e293b',
    borderColor: '#334155',
  },
  unreadCard: {
    backgroundColor: '#fff1f2',
    borderColor: '#fecdd3',
  },
  unreadCardDark: {
    backgroundColor: '#4c1d9520',
    borderColor: '#e11d4840',
  },
  iconContainer: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#e11d4820',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  textContainer: {
    flex: 1,
  },
  title: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
    marginBottom: 4,
  },
  message: {
    fontSize: 14,
    color: '#475569',
    marginBottom: 8,
  },
  time: {
    fontSize: 12,
    color: '#94a3b8',
  },
  unreadDot: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#e11d48',
    marginLeft: 8,
  },
});
