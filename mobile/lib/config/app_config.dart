class AppConfig {
  static const String appName = 'Wazabiashara';
  static const String appTagline = 'Your Business, In Your Hands';

  // API Configuration
  static const String baseUrl = 'http://10.0.2.2:8000'; // Android emulator -> host machine
  static const String apiVersion = '/api/v1';
  static const Duration apiTimeout = Duration(seconds: 30);

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String businessKey = 'business_data';
  static const String languageKey = 'app_language';
  static const String themeKey = 'app_theme';
  static const String pinKey = 'user_pin';
  static const String onboardingKey = 'onboarding_completed';

  // Currency
  static const String currency = 'TZS';
  static const String currencySymbol = 'TSh';

  // Pagination
  static const int pageSize = 20;

  // Sync
  static const Duration syncInterval = Duration(minutes: 5);
}
