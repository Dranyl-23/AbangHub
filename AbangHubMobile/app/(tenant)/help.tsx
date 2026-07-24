import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import { useTheme } from '../../src/context/ThemeContext';

export default function HelpScreen() {
  const { isDarkMode } = useTheme();

  return (
    <ScrollView style={[styles.container, isDarkMode && styles.containerDark]}>
      <View style={[styles.header, isDarkMode && styles.headerDark]}>
        <TouchableOpacity onPress={() => router.push('/(tenant)/profile' as any)} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={isDarkMode ? "#f8fafc" : "#0f172a"} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, isDarkMode && styles.textDark]}>Help & Support</Text>
        <View style={{ width: 40 }} />
      </View>

      <View style={styles.content}>
        <Text style={[styles.sectionTitle, isDarkMode && styles.textDark]}>Frequently Asked Questions</Text>
        
        <View style={[styles.faqItem, isDarkMode && styles.cardDark]}>
          <Text style={[styles.question, isDarkMode && styles.textDark]}>How do I apply for a property?</Text>
          <Text style={[styles.answer, isDarkMode && styles.textDarkMuted]}>
            You can apply by navigating to the Explore page, selecting a property, and clicking the "Apply Now" button. Make sure your profile and ID are verified.
          </Text>
        </View>

        <View style={[styles.faqItem, isDarkMode && styles.cardDark]}>
          <Text style={[styles.question, isDarkMode && styles.textDark]}>How do I pay my rent?</Text>
          <Text style={[styles.answer, isDarkMode && styles.textDarkMuted]}>
            Rent payments can be made through your AbangHub Wallet or via bank transfer directly to your landlord, as indicated in your lease agreement.
          </Text>
        </View>

        <View style={[styles.faqItem, isDarkMode && styles.cardDark]}>
          <Text style={[styles.question, isDarkMode && styles.textDark]}>How do I contact my landlord?</Text>
          <Text style={[styles.answer, isDarkMode && styles.textDarkMuted]}>
            Once your application is approved, your landlord's contact information will be visible in the "My Applications" tab.
          </Text>
        </View>

        <Text style={[styles.sectionTitle, { marginTop: 24 }, isDarkMode && styles.textDark]}>Contact Us</Text>
        
        <TouchableOpacity style={[styles.contactCard, isDarkMode && styles.cardDark]}>
          <Ionicons name="mail" size={24} color="#e11d48" />
          <View style={styles.contactInfo}>
            <Text style={[styles.contactLabel, isDarkMode && styles.textDark]}>Email Support</Text>
            <Text style={[styles.contactValue, isDarkMode && styles.textDarkMuted]}>support@abanghub.com</Text>
          </View>
        </TouchableOpacity>

        <TouchableOpacity style={[styles.contactCard, isDarkMode && styles.cardDark]}>
          <Ionicons name="call" size={24} color="#e11d48" />
          <View style={styles.contactInfo}>
            <Text style={[styles.contactLabel, isDarkMode && styles.textDark]}>Phone Support</Text>
            <Text style={[styles.contactValue, isDarkMode && styles.textDarkMuted]}>1-800-ABANGHUB</Text>
          </View>
        </TouchableOpacity>

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
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#0f172a', marginBottom: 16 },
  cardDark: { backgroundColor: '#1e293b', borderColor: '#334155' },
  faqItem: {
    backgroundColor: '#ffffff', padding: 16, borderRadius: 12, marginBottom: 12,
    borderWidth: 1, borderColor: '#e2e8f0',
  },
  question: { fontSize: 16, fontWeight: 'bold', color: '#334155', marginBottom: 8 },
  answer: { fontSize: 14, color: '#64748b', lineHeight: 22 },
  contactCard: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffffff',
    padding: 16, borderRadius: 12, marginBottom: 12, borderWidth: 1, borderColor: '#e2e8f0',
  },
  contactInfo: { marginLeft: 16 },
  contactLabel: { fontSize: 16, fontWeight: 'bold', color: '#334155' },
  contactValue: { fontSize: 14, color: '#64748b', marginTop: 4 },
});
