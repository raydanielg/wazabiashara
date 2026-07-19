import 'package:flutter/material.dart';

class AppRoutes {
  static const String splash = '/splash';
  static const String login = '/login';
  static const String register = '/register';
  static const String otp = '/otp';
  static const String dashboard = '/dashboard';
  static const String pos = '/pos';
  static const String products = '/products';
  static const String productDetail = '/product-detail';
  static const String sales = '/sales';
  static const String saleDetail = '/sale-detail';
  static const String customers = '/customers';
  static const String customerDetail = '/customer-detail';
  static const String reports = '/reports';
  static const String settings = '/settings';
  static const String profile = '/profile';

  static Route<dynamic> onGenerateRoute(RouteSettings settings) {
    final args = settings.arguments;

    switch (settings.name) {
      case splash:
        return _page(const _Placeholder(title: 'Splash'));
      case login:
        return _page(const _Placeholder(title: 'Login'));
      case register:
        return _page(const _Placeholder(title: 'Register'));
      case otp:
        return _page(const _Placeholder(title: 'OTP'));
      case dashboard:
        return _page(const _Placeholder(title: 'Dashboard'));
      case pos:
        return _page(const _Placeholder(title: 'POS'));
      case products:
        return _page(const _Placeholder(title: 'Products'));
      case sales:
        return _page(const _Placeholder(title: 'Sales'));
      case customers:
        return _page(const _Placeholder(title: 'Customers'));
      case reports:
        return _page(const _Placeholder(title: 'Reports'));
      case AppRoutes.settings:
        return _page(const _Placeholder(title: 'Settings'));
      case profile:
        return _page(const _Placeholder(title: 'Profile'));
      default:
        return _page(const _Placeholder(title: 'Not Found'));
    }
  }

  static PageRoute _page(Widget widget) {
    return MaterialPageRoute(builder: (_) => widget);
  }
}

class _Placeholder extends StatelessWidget {
  final String title;
  const _Placeholder({required this.title});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.construction, size: 64, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              '$title — Coming Soon',
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
