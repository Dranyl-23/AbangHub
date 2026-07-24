import React, { useState, useEffect, useRef } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, FlatList, KeyboardAvoidingView, Platform, ActivityIndicator, Keyboard, Image } from 'react-native';
import { useLocalSearchParams, router, Stack } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import apiClient from '../../src/api/client';
import { Message, User } from '../../src/types';
import { useTheme } from '../../src/context/ThemeContext';
import * as SecureStore from 'expo-secure-store';

export default function ChatScreen() {
  const { userId, propertyId } = useLocalSearchParams();
  const { isDarkMode } = useTheme();
  const insets = useSafeAreaInsets();
  const [keyboardHeight, setKeyboardHeight] = useState(0);

  useEffect(() => {
    if (Platform.OS === 'android') {
      const showSubscription = Keyboard.addListener('keyboardDidShow', (e) => {
        setKeyboardHeight(e.endCoordinates.height);
      });
      const hideSubscription = Keyboard.addListener('keyboardDidHide', () => {
        setKeyboardHeight(0);
      });
      return () => {
        showSubscription.remove();
        hideSubscription.remove();
      };
    }
  }, []);
  
  const [user, setUser] = useState<User | null>(null);
  const [messages, setMessages] = useState<Message[]>([]);
  const [otherUser, setOtherUser] = useState<User | null>(null);
  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  
  const flatListRef = useRef<FlatList>(null);

  useEffect(() => {
    const loadUser = async () => {
      const userData = await SecureStore.getItemAsync('userData');
      if (userData) {
        setUser(JSON.parse(userData));
      }
    };
    loadUser();
    fetchMessages();

    // Optional: Implement a polling mechanism for new messages here
    // const interval = setInterval(fetchMessages, 5000);
    // return () => clearInterval(interval);
  }, []);

  const fetchMessages = async () => {
    try {
      const parsedPropertyId = parseInt(propertyId as string, 10);
      const pid = !isNaN(parsedPropertyId) ? parsedPropertyId : 0;
      const response = await apiClient.get(`/messages/${userId}/${pid}`);
      setMessages(response.data.messages);
      setOtherUser(response.data.other_user);
    } catch (error) {
      console.error('Error fetching chat history:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSend = async () => {
    if (!inputText.trim() || sending) return;

    setSending(true);
    const tempText = inputText.trim();
    setInputText('');

    try {
      const parsedPropertyId = parseInt(propertyId as string, 10);
      const payload = {
        receiver_id: userId,
        property_id: (!isNaN(parsedPropertyId) && parsedPropertyId > 0) ? parsedPropertyId : null,
        content: tempText,
      };

      const response = await apiClient.post('/messages', payload);
      const newMessage = response.data.data;
      
      setMessages(prev => [...prev, newMessage]);
      setTimeout(() => flatListRef.current?.scrollToEnd({ animated: true }), 100);
    } catch (error: any) {
      console.error('Error sending message:', error);
      if (error.response) {
        console.error('Validation errors:', JSON.stringify(error.response.data, null, 2));
      }
      setInputText(tempText); // restore text on failure
    } finally {
      setSending(false);
    }
  };

  const renderMessage = ({ item }: { item: Message }) => {
    const isMine = item.sender_id === user?.id;
    return (
      <View style={[styles.messageRow, isMine ? styles.myMessageRow : styles.theirMessageRow]}>
        <View style={[
          styles.messageBubble, 
          isMine ? styles.myMessageBubble : (isDarkMode ? styles.theirMessageBubbleDark : styles.theirMessageBubble)
        ]}>
          <Text style={[styles.messageText, isMine ? styles.myMessageText : (isDarkMode && styles.theirMessageTextDark)]}>
            {item.content}
          </Text>
          <Text style={[styles.timeText, isMine && styles.myTimeText]}>
            {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </Text>
        </View>
      </View>
    );
  };

  return (
    <View style={[styles.container, isDarkMode && styles.containerDark]}>
      <Stack.Screen options={{ headerShown: false }} />
      <View style={[styles.header, isDarkMode && styles.headerDark, { paddingTop: insets.top + 16 }]}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? '#f8fafc' : '#0f172a'} />
        </TouchableOpacity>
        
        {otherUser && (
          <View style={styles.headerAvatarContainer}>
            {otherUser.profile_image ? (
              <Image 
                source={{ uri: otherUser.profile_image.startsWith('http') ? otherUser.profile_image : `${apiClient.defaults.baseURL?.replace('/api', '')}${otherUser.profile_image.startsWith('/') ? otherUser.profile_image : '/storage/' + otherUser.profile_image}` }} 
                style={styles.headerAvatarImage} 
              />
            ) : (
              <View style={styles.headerAvatarInitialContainer}>
                <Text style={styles.headerAvatarInitial}>
                  {(otherUser.full_name?.[0] || otherUser.username?.[0] || '?').toUpperCase()}
                </Text>
              </View>
            )}
          </View>
        )}

        <View style={styles.headerInfo}>
          <Text style={[styles.headerName, isDarkMode && styles.textDark]}>
            {otherUser ? (otherUser.full_name || otherUser.username || 'User') : 'Loading...'}
          </Text>
          {otherUser?.user_type === 'landlord' && (
            <Text style={styles.headerRole}>Landlord</Text>
          )}
        </View>
      </View>

      <View style={{ flex: 1, paddingBottom: Platform.OS === 'android' ? (keyboardHeight > 0 ? keyboardHeight + 20 : 0) : 0 }}>
        <KeyboardAvoidingView 
          style={{ flex: 1 }}
          behavior={Platform.OS === 'ios' ? 'padding' : undefined}
          keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 0}
        >
      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#e11d48" />
        </View>
      ) : (
        <FlatList
          ref={flatListRef}
          data={messages}
          keyExtractor={(item, index) => item.id ? item.id.toString() : `temp-${index}`}
          renderItem={renderMessage}
          contentContainerStyle={styles.listContent}
          onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: true })}
          onLayout={() => flatListRef.current?.scrollToEnd({ animated: false })}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Text style={styles.emptyText}>No messages yet.</Text>
              <Text style={styles.emptySubText}>Send a message to start chatting!</Text>
            </View>
          }
        />
      )}

      <View style={[styles.inputContainer, isDarkMode && styles.inputContainerDark, { paddingBottom: Math.max(insets.bottom, 12) }]}>
        <TextInput
          style={[styles.input, isDarkMode && styles.inputDark]}
          placeholder="Type a message..."
          placeholderTextColor="#94a3b8"
          value={inputText}
          onChangeText={setInputText}
          multiline
        />
        <TouchableOpacity 
          style={[styles.sendButton, (!inputText.trim() || sending) && styles.sendButtonDisabled]}
          onPress={handleSend}
          disabled={!inputText.trim() || sending}
        >
          {sending ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Ionicons name="send" size={20} color="#ffffff" />
          )}
        </TouchableOpacity>
      </View>
        </KeyboardAvoidingView>
      </View>
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
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 16,
    paddingBottom: 16,
    paddingHorizontal: 16,
    backgroundColor: '#ffffff',
    shadowOpacity: 0.05,
    shadowRadius: 5,
    elevation: 3,
  },
  headerDark: {
    backgroundColor: '#1e293b',
    borderBottomColor: '#334155',
  },
  backButton: {
    marginRight: 12,
    padding: 4,
  },
  headerAvatarContainer: {
    marginRight: 12,
  },
  headerAvatarImage: {
    width: 40,
    height: 40,
    borderRadius: 20,
  },
  headerAvatarInitialContainer: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#e2e8f0',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerAvatarInitial: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#64748b',
  },
  headerInfo: {
    flex: 1,
  },
  headerName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#0f172a',
  },
  headerRole: {
    fontSize: 12,
    color: '#64748b',
  },
  textDark: {
    color: '#f8fafc',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  listContent: {
    padding: 16,
    paddingBottom: 32,
  },
  emptyContainer: {
    alignItems: 'center',
    marginTop: 40,
  },
  emptyText: {
    color: '#64748b',
    fontSize: 16,
    fontWeight: '600',
  },
  emptySubText: {
    color: '#94a3b8',
    fontSize: 14,
    marginTop: 4,
  },
  messageRow: {
    flexDirection: 'row',
    marginBottom: 12,
  },
  myMessageRow: {
    justifyContent: 'flex-end',
  },
  theirMessageRow: {
    justifyContent: 'flex-start',
  },
  messageBubble: {
    maxWidth: '80%',
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 20,
  },
  myMessageBubble: {
    backgroundColor: '#e11d48',
    borderBottomRightRadius: 4,
  },
  theirMessageBubble: {
    backgroundColor: '#ffffff',
    borderBottomLeftRadius: 4,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  theirMessageBubbleDark: {
    backgroundColor: '#1e293b',
    borderBottomLeftRadius: 4,
    borderWidth: 1,
    borderColor: '#334155',
  },
  messageText: {
    fontSize: 15,
    lineHeight: 22,
    color: '#0f172a',
  },
  myMessageText: {
    color: '#ffffff',
  },
  theirMessageTextDark: {
    color: '#f8fafc',
  },
  timeText: {
    fontSize: 10,
    color: '#94a3b8',
    alignSelf: 'flex-end',
    marginTop: 4,
  },
  myTimeText: {
    color: 'rgba(255,255,255,0.7)',
  },
  inputContainer: {
    flexDirection: 'row',
    padding: 12,
    backgroundColor: '#ffffff',
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
    alignItems: 'flex-end',
  },
  inputContainerDark: {
    backgroundColor: '#1e293b',
    borderTopColor: '#334155',
  },
  input: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 12,
    maxHeight: 100,
    fontSize: 15,
    color: '#0f172a',
    marginRight: 12,
  },
  inputDark: {
    backgroundColor: '#0f172a',
    color: '#f8fafc',
  },
  sendButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#e11d48',
    justifyContent: 'center',
    alignItems: 'center',
  },
  sendButtonDisabled: {
    backgroundColor: '#fda4af',
  }
});
