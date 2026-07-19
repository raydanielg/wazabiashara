import 'package:flutter/material.dart';
import '../screens/auth/login_screen.dart';
import '../screens/onboarding/onboarding_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/auth/forgot_password_screen.dart';
import '../screens/auth/otp_screen.dart';
import '../screens/auth/business_setup_screen.dart';
import '../screens/dashboard/dashboard_screen.dart';
import '../screens/sales/sales_screen.dart';
import '../screens/customers/customers_screen.dart';

class AppRoutes {
  static const String splash = '/splash';
  static const String onboarding = '/onboarding';
  static const String login = '/login';
  static const String register = '/register';
  static const String forgotPassword = '/forgot-password';
  static const String otp = '/otp';
  static const String businessSetup = '/business-setup';
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
      case forgotPassword:
        return _page(const ForgotPasswordScreen());
      case otp:
        final phone = args is String ? args : null;
        return _page(OtpScreen(phone: phone));
      case businessSetup:
        return _page(const BusinessSetupScreen());
      case dashboard:
        return _page(const DashboardScreen());
      case sales:
        return _page(const SalesScreen());
      case customers:
        return _page(const CustomersScreen());
      default:
        return _page(const Scaffold(
          body: Center(child: Text('Page not found')),
        ));
    }
  }

  static PageRoute _page(Widget widget) {
    return MaterialPageRoute(builder: (_) => widget);
  }
}
