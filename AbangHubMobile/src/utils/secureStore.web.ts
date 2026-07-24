/**
 * Web polyfill for expo-secure-store.
 * On web, SecureStore is not available, so we fall back to localStorage.
 * WARNING: localStorage is NOT encrypted — use only for non-sensitive data on web.
 * This file is auto-selected by Metro bundler on web via the "browser" field.
 */

export async function setItemAsync(key: string, value: string): Promise<void> {
  try {
    localStorage.setItem(key, value);
  } catch {
    console.warn('[SecureStore web polyfill] localStorage write failed for key:', key);
  }
}

export async function getItemAsync(key: string): Promise<string | null> {
  try {
    return localStorage.getItem(key);
  } catch {
    console.warn('[SecureStore web polyfill] localStorage read failed for key:', key);
    return null;
  }
}

export async function deleteItemAsync(key: string): Promise<void> {
  try {
    localStorage.removeItem(key);
  } catch {
    console.warn('[SecureStore web polyfill] localStorage delete failed for key:', key);
  }
}
