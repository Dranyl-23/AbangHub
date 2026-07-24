import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, ActivityIndicator, Image, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, router } from 'expo-router';
import apiClient from '../../src/api/client';
import { Message, User } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';
import * as SecureStore from 'expo-secure-store';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function MessagesScreen() {
  const [user, setUser] = useState<User | null>(null);
  const { isDarkMode } = useTheme();
  const [conversations, setConversations] = useState<Message[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchConversations = async () => {
    try {
      const response = await apiClient.get('/messages');
      setConversations(response.data.data);
    } catch (error) {
      console.error('Error fetching conversations:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      const loadUser = async () => {
        const userData = await SecureStore.getItemAsync('userData');
        if (userData) {
          setUser(JSON.parse(userData));
        }
      };
      loadUser();
      setLoading(true);
      fetchConversations();
    }, [])
  );

  const handleRefresh = () => {
    setRefreshing(true);
    fetchConversations();
  };

  const renderConversation = ({ item }: { item: Message }) => {
    // The other user is either the sender or receiver
    const otherUser = item.sender_id === user?.id ? item.receiver : item.sender;
    if (!otherUser) return null;

    // Check if unread (if current user is the receiver and it's not read)
    const isUnread = item.receiver_id === user?.id && !item.is_read;
    const propertyId = item.property_id || 0;

    return (
      <TouchableOpacity 
        style={[styles.chatItem, isDarkMode && styles.chatItemDark]} 
        onPress={() => router.push(`/messages/${otherUser.id}?propertyId=${propertyId}` as any)}
      >
        <View style={styles.avatarContainer}>
          {otherUser.profile_image ? (
            <Image 
              source={{ uri: otherUser.profile_image.startsWith('http') ? otherUser.profile_image : `${apiClient.defaults.baseURL?.replace('/api', '')}${otherUser.profile_image.startsWith('/') ? otherUser.profile_image : '/storage/' + otherUser.profile_image}` }} 
              style={{ width: '100%', height: '100%', borderRadius: 25 }} 
            />
          ) : (
            <Text style={styles.avatarInitial}>
              {(otherUser.full_name?.[0] || otherUser.username?.[0] || '?').toUpperCase()}
            </Text>
          )}
        </View>
        <View style={styles.chatInfo}>
          <View style={styles.chatHeader}>
            <Text style={[styles.userName, isDarkMode && styles.textDark, isUnread && styles.unreadText]} numberOfLines={1}>
              {otherUser.full_name || otherUser.username || 'User'}
            </Text>
            <Text style={styles.timeText}>
              {new Date(item.created_at).toLocaleDateString()}
            </Text>
          </View>
          {item.property && (
            <Text style={styles.propertyTitle} numberOfLines={1}>
              <Ionicons name="home-outline" size={12} /> {item.property.title}
            </Text>
          )}
          <Text style={[styles.lastMessage, isUnread && styles.unreadMessageText]} numberOfLines={1}>
            {item.sender_id === user?.id ? 'You: ' : ''}{item.content}
          </Text>
        </View>
        {isUnread && <View style={styles.unreadDot} />}
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
    <SafeAreaView style={[styles.container, isDarkMode && styles.containerDark]} edges={['top']}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Messages</Text>
      </View>

      <FlatList
        data={conversations}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderConversation}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={['#e11d48']} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="chatbubbles-outline" size={64} color={isDarkMode ? "#334155" : "#e2e8f0"} />
            <Text style={[styles.emptyTitle, isDarkMode && styles.textDark]}>No messages yet</Text>
            <Text style={styles.emptySubtitle}>Start a conversation with a landlord when you find a property you like.</Text>
          </View>
        }
      />
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
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
  },
  header: {
    paddingTop: 16,
    paddingHorizontal: 20,
    paddingBottom: 16,
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
  listContent: {
    padding: 0,
    paddingBottom: 100,
  },
  textDark: {
    color: '#f8fafc',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 80,
    paddingHorizontal: 32,
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
    lineHeight: 22,
  },
  chatItem: {
    flexDirection: 'row',
    padding: 16,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
    alignItems: 'center',
  },
  chatItemDark: {
    backgroundColor: '#1e293b',
    borderBottomColor: '#334155',
  },
  avatarContainer: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: '#0ea5e9',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  avatarInitial: {
    color: '#ffffff',
    fontSize: 22,
    fontWeight: 'bold',
  },
  chatInfo: {
    flex: 1,
    justifyContent: 'center',
  },
  chatHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  userName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a',
    flex: 1,
    marginRight: 8,
  },
  timeText: {
    fontSize: 12,
    color: '#94a3b8',
  },
  propertyTitle: {
    fontSize: 12,
    color: '#64748b',
    marginBottom: 4,
  },
  lastMessage: {
    fontSize: 14,
    color: '#64748b',
  },
  unreadText: {
    fontWeight: '800',
  },
  unreadMessageText: {
    color: '#0f172a',
    fontWeight: '600',
  },
  unreadDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#e11d48',
    marginLeft: 12,
  },
});
