import 'package:flutter/material.dart';
import '../services/storage_service.dart';
import '../services/translation_service.dart';

class ThemeProvider extends ChangeNotifier {
  final _storage = StorageService();
  final _translation = TranslationService();

  bool _isDarkMode = false;
  String _language = 'en';

  bool get isDarkMode => _isDarkMode;
  String get language => _language;
  TranslationService get translation => _translation;

  Future<void> init() async {
    _isDarkMode = await _storage.getDarkMode();
    _language = await _storage.getLanguage();
    await _translation.init();
    if (_language != 'en') {
      await _translation.loadLanguage(_language);
    }
    notifyListeners();
  }

  Future<void> toggleDarkMode() async {
    _isDarkMode = !_isDarkMode;
    await _storage.setDarkMode(_isDarkMode);
    notifyListeners();
  }

  Future<void> setLanguage(String lang) async {
    _language = lang;
    await _storage.setLanguage(lang);
    await _translation.loadLanguage(lang);
    notifyListeners();
  }

  String t(String key) => _translation.t(key);
}
