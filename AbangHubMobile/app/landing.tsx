import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Image, ImageBackground, StatusBar } from 'react-native';
import { router } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';

export default function LandingScreen() {
  return (
    <ImageBackground 
      source={require('../assets/images/landing-bg.jpg')} 
      style={styles.landingBackground}
    >
      <StatusBar barStyle="light-content" />
      <LinearGradient
        colors={['transparent', 'rgba(15, 23, 42, 0.8)', 'rgba(15, 23, 42, 1)']}
        locations={[0, 0.5, 1]}
        style={styles.overlay}
      >
        <View style={styles.landingContent}>
          <View style={styles.brandingContainer}>
            <View style={styles.logoWrapper}>
              <Image source={require('../assets/images/logo.jpg')} style={styles.landingLogo} />
            </View>
            <Text style={styles.landingTitle}>AbangHub</Text>
            <Text style={styles.landingSubtitle}>Find your perfect home today</Text>
          </View>
          <TouchableOpacity style={styles.landingButton} onPress={() => router.push('/login' as any)}>
            <Text style={styles.landingButtonText}>Get Started</Text>
          </TouchableOpacity>
        </View>
      </LinearGradient>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  landingBackground: {
    flex: 1,
    width: '100%',
    height: '100%',
  },
  overlay: {
    flex: 1,
    justifyContent: 'flex-end',
    padding: 24,
  },
  landingContent: {
    alignItems: 'center',
    marginBottom: 48,
  },
  brandingContainer: {
    alignItems: 'center',
    marginBottom: 64,
  },
  logoWrapper: {
    width: 100,
    height: 100,
    borderRadius: 50,
    padding: 4,
    backgroundColor: '#fff',
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    marginBottom: 24,
  },
  landingLogo: {
    width: '100%',
    height: '100%',
    borderRadius: 46,
  },
  landingTitle: {
    fontSize: 48,
    fontWeight: '800',
    color: '#fff',
    marginBottom: 12,
    letterSpacing: -1,
  },
  landingSubtitle: {
    fontSize: 18,
    color: '#e2e8f0',
    fontWeight: '500',
    textAlign: 'center',
  },
  landingButton: {
    backgroundColor: '#e11d48',
    width: '100%',
    paddingVertical: 18,
    borderRadius: 16,
    alignItems: 'center',
    elevation: 4,
    shadowColor: '#e11d48',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.4,
    shadowRadius: 8,
  },
  landingButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: '700',
  },
});
