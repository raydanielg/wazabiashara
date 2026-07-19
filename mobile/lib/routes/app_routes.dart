import 'package:flutter/material.dart';
import '../screens/auth/login_screen.dart';
import '../screens/onboarding/onboarding_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/auth/otp_screen.dart';
import '../screens/dashboard/dashboard_screen.dart';

class AppRoutes {
  static const String splash = '/splash';
  static const String onboarding = '/onboarding';
  static const String login = '/login';
  static const String register = '/register';
  static const String otp = '/otp';
  static const String dashboard = '/dashboard';
  static const String pos = '/pos';
  static const String products = '/products';
  static const String sales = '/sales';
  static const String customers = '/customers';
  static const String reports = '/reports';
  static const String settings = '/settings';
  static const String profile = '/profile';

  static Route<dynamic> onGenerateRoute(RouteSettings settings) {
    final args = settings.arguments;

    switch (settings.name) {
      case onboarding:
        return _page(const OnboardingScreen());
      case login:
        return _page(const LoginScreen());
      case register:
        return _page(const RegisterScreen());
      case otp:
        final phone = args is String ? args : null;
        return _page(OtpScreen(phone: phone));
      case dashboard:
        return _page(const DashboardScreen());
      case pos:
        return _page(const _Placeholder(title: 'POS', icon: Icons.point_of_sale));
      case products:
        return _page(const _Placeholder(title: 'Products', icon: Icons.inventory_2_outlined));
      case sales:
        return _page(const _Placeholder(title: 'Sales', icon: Icons.receipt_long_outlined));
      case customers:
        return _page(const _Placeholder(title: 'Customers', icon: Icons.people_outline));
      case reports:
        return _page(const _Placeholder(title: 'Reports', icon: Icons.bar_chart_outlined));
      case AppRoutes.settings:
        return _page(const _Placeholder(title: 'Settings', icon: Icons.settings_outlined));
      case profile:
        return _page(const _Placeholder(title: 'Profile', icon: Icons.person_outline));
      default:
        return _page(const _Placeholder(title: 'Not Found', icon: Icons.error_outline));
    }
  }

  static PageRoute _page(Widget widget) {
    return MaterialPageRoute(builder: (_) => widget);
  }
}

class _Placeholder extends StatelessWidget {
  final String title;
  final IconData icon;

  const _Placeholder({required this.title, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 64, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              '$title - Coming Soon',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey[500],
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'This screen is under construction',
              style: TextStyle(fontSize: 14, color: Colors.grey[400]),
            ),
          ],
        ),
      ),
    );
  }
}
