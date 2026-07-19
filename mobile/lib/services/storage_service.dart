import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class StorageService {
  static final StorageService _instance = StorageService._internal();
  factory StorageService() => _instance;

  const StorageService._internal();

  final _secureStorage = const FlutterSecureStorage();

  // Secure storage (for sensitive data)
  Future<void> setSecure(String key, String value) async {
    await _secureStorage.write(key: key, value: value);
  }

  Future<String?> getSecure(String key) async {
    return await _secureStorage.read(key: key);
  }

  Future<void> deleteSecure(String key) async {
    await _secureStorage.delete(key: key);
  }

  // Shared preferences (for non-sensitive data)
  Future<void> setString(String key, String value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(key, value);
  }

  Future<String?> getString(String key) async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(key);
  }

  Future<void> setBool(String key, bool value) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(key, value);
  }

  Future<bool?> getBool(String key) async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(key);
  }

  Future<void> remove(String key) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(key);
  }

  // Convenience methods for auth
  Future<void> saveToken(String token) async {
    await setSecure(AppConfig.tokenKey, token);
  }

  Future<String?> getToken() async {
    return await getSecure(AppConfig.tokenKey);
  }

  Future<void> saveUser(Map<String, dynamic> user) async {
    await setSecure(AppConfig.userKey, jsonEncode(user));
  }

  Future<Map<String, dynamic>?> getUser() async {
    final data = await getSecure(AppConfig.userKey);
    if (data != null) {
      return jsonDecode(data) as Map<String, dynamic>;
    }
    return null;
  }

  Future<void> clearAuth() async {
    await deleteSecure(AppConfig.tokenKey);
    await deleteSecure(AppConfig.userKey);
    await deleteSecure(AppConfig.businessKey);
  }

  // Language preference
  Future<void> setLanguage(String lang) async {
    await setString(AppConfig.languageKey, lang);
  }

  Future<String> getLanguage() async {
    return await getString(AppConfig.languageKey) ?? 'en';
  }

  // Theme preference
  Future<void> setDarkMode(bool isDark) async {
    await setBool(AppConfig.themeKey, isDark);
  }

  Future<bool> getDarkMode() async {
    return await getBool(AppConfig.themeKey) ?? false;
  }
}
