import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import { useTheme } from '../../src/context/ThemeContext';

export default function LegalScreen() {
  const { isDarkMode } = useTheme();

  return (
    <ScrollView style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <TouchableOpacity onPress={() => router.push('/(landlord)/profile' as any)} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? "#f8fafc" : "#0f172a"} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Terms & Privacy</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.content}>
        <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Terms and Conditions</Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          Welcome to AbangHub. By accessing or using our mobile application, you agree to be bound by these Terms and Conditions and our Privacy Policy.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          1. Use of the App: You must provide accurate and complete information when creating an account. You are responsible for maintaining the confidentiality of your account credentials.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          2. Rental Applications: Submitting an application does not guarantee approval. Landlords have the final say on all tenancy agreements.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          3. Payments: All transactions made through the AbangHub Wallet are subject to processing fees and our payment terms.
        </Text>

        <Text style={[styles.sectionTitle, { marginTop: 24 }, isDarkMode && styles.textDark]}>Privacy Policy</Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal data.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          1. Data Collection: We collect information you provide directly to us, such as your name, email, phone number, and government-issued IDs for verification purposes.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          2. Data Usage: We use your data to provide, maintain, and improve our services, process transactions, and communicate with you.
        </Text>
        <Text style={[styles.paragraph, isDarkMode && styles.textDarkMuted]}>
          3. Data Sharing: We may share your verified profile information with prospective landlords when you submit a rental application. We do not sell your personal data to third parties.
        </Text>

        <Text style={[styles.footerText, isDarkMode && styles.textDarkMuted]}>
          Last updated: October 2026
        </Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  containerDark: { backgroundColor: '#0f172a' },
  header: {
    flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
    paddingTop: 50, paddingBottom: 20, paddingHorizontal: 20, backgroundColor: '#ffffff',
    borderBottomWidth: 1, borderBottomColor: '#e2e8f0',
  },
  headerDark: { backgroundColor: '#1e293b', borderBottomColor: '#334155' },
  backButton: { padding: 8, marginLeft: -8 },
  headerTitle: { fontSize: 20, fontWeight: 'bold', color: '#0f172a' },
  textDark: { color: '#f8fafc' },
  textDarkMuted: { color: '#94a3b8' },
  content: { padding: 24 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#0f172a', marginBottom: 12 },
  paragraph: { fontSize: 14, color: '#475569', lineHeight: 22, marginBottom: 12 },
  footerText: { fontSize: 12, color: '#94a3b8', textAlign: 'center', marginTop: 32, fontStyle: 'italic' },
});
