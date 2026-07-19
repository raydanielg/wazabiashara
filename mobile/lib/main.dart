import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'theme/app_theme.dart';
import 'providers/auth_provider.dart';
import 'providers/theme_provider.dart';
import 'providers/dashboard_provider.dart';
import 'services/storage_service.dart';
import 'services/notification_service.dart';
import 'routes/app_routes.dart';
import 'screens/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final storage = StorageService();
  final isDark = await storage.getDarkMode();
  final language = await storage.getLanguage();

  // Sets up notification channels; safe before sign-in. Permission is
  // requested later, once the user actually reaches the dashboard.
  await NotificationService.instance.init();

  runApp(WazabiasharaApp(initialDarkMode: isDark, initialLanguage: language));
}

class WazabiasharaApp extends StatelessWidget {
  final bool initialDarkMode;
  final String initialLanguage;

  const WazabiasharaApp({
    super.key,
    required this.initialDarkMode,
    required this.initialLanguage,
  });

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => ThemeProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
      ],
      child: Consumer<ThemeProvider>(
        builder: (context, themeProvider, _) {
          return MaterialApp(
            title: 'Wazabiashara',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: themeProvider.isDarkMode ? ThemeMode.dark : ThemeMode.light,
            home: const SplashScreen(),
            onGenerateRoute: AppRoutes.onGenerateRoute,
          );
        },
      ),
    );
  }
}
