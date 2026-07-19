import 'dart:convert';
import 'package:flutter/services.dart';

class TranslationService {
  static final TranslationService _instance = TranslationService._internal();
  factory TranslationService() => _instance;

  TranslationService._internal();

  Map<String, dynamic> _translations = {};
  String _currentLanguage = 'en';

  String get currentLanguage => _currentLanguage;

  Future<void> init() async {
    await loadLanguage('en');
  }

  Future<void> loadLanguage(String lang) async {
    try {
      final jsonString = await rootBundle.loadString('assets/translations/$lang.json');
      _translations = jsonDecode(jsonString) as Map<String, dynamic>;
      _currentLanguage = lang;
    } catch (e) {
      // Fallback to English
      if (lang != 'en') {
        await loadLanguage('en');
      }
    }
  }

  String translate(String key) {
    final keys = key.split('.');
    dynamic value = _translations;

    for (final k in keys) {
      if (value is Map<String, dynamic>) {
        value = value[k];
      } else {
        return key;
      }
    }

    if (value is String) {
      return value;
    }
    return key;
  }

  String t(String key) => translate(key);
}
