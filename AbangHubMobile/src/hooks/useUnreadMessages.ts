import { useState, useEffect, useRef, useCallback } from 'react';
import { AppState, AppStateStatus } from 'react-native';
import apiClient from '../api/client';

const POLL_INTERVAL_ACTIVE = 30000;  // 30s when app is active
const POLL_INTERVAL_BACKGROUND = 0;  // No polling in background

/**
 * Custom hook for smart unread message polling.
 * - Pauses polling when app is in background (saves battery & server load)
 * - Polls every 30s when active (vs previous 10s — 3x less server load)
 * - Cleans up interval on unmount
 */
export function useUnreadMessages() {
  const [unreadCount, setUnreadCount] = useState(0);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const appStateRef = useRef<AppStateStatus>(AppState.currentState);

  const fetchUnreadCount = useCallback(async () => {
    try {
      const response = await apiClient.get('/messages/unread-count');
      setUnreadCount(response.data.unread_count ?? 0);
    } catch {
      // Silently ignore — user may be unauthenticated or offline
    }
  }, []);

  const startPolling = useCallback(() => {
    if (intervalRef.current) return; // Already running
    fetchUnreadCount(); // Immediate fetch on resume
    intervalRef.current = setInterval(fetchUnreadCount, POLL_INTERVAL_ACTIVE);
  }, [fetchUnreadCount]);

  const stopPolling = useCallback(() => {
    if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }
  }, []);

  useEffect(() => {
    // Start polling immediately
    startPolling();

    // Listen for app state changes
    const subscription = AppState.addEventListener('change', (nextAppState: AppStateStatus) => {
      if (
        appStateRef.current.match(/inactive|background/) &&
        nextAppState === 'active'
      ) {
        // App came to foreground — resume polling
        startPolling();
      } else if (nextAppState.match(/inactive|background/)) {
        // App went to background — stop polling to save battery
        stopPolling();
      }
      appStateRef.current = nextAppState;
    });

    return () => {
      stopPolling();
      subscription.remove();
    };
  }, [startPolling, stopPolling]);

  return unreadCount;
}
