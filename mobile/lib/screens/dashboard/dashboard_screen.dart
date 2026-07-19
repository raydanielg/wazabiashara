import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/app_theme.dart';
import '../providers/theme_provider.dart';
import '../providers/auth_provider.dart';
import '../utils/format_util.dart';
import '../widgets/stat_card.dart';
import '../widgets/empty_state.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _currentIndex = 0;

  final _pages = [
    const _DashboardHome(),
    const _PlaceholderPage(title: 'POS', icon: Icons.point_of_sale),
    const _PlaceholderPage(title: 'Products', icon: Icons.inventory_2_outlined),
    const _PlaceholderPage(title: 'Reports', icon: Icons.bar_chart),
    const _PlaceholderPage(title: 'Settings', icon: Icons.settings_outlined),
  ];

  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();

    return Scaffold(
      body: _pages[_currentIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (i) => setState(() => _currentIndex = i),
        items: [
          BottomNavigationBarItem(
            icon: const Icon(Icons.dashboard_outlined),
            activeIcon: const Icon(Icons.dashboard),
            label: theme.t('nav.dashboard'),
          ),
          BottomNavigationBarItem(
            icon: const Icon(Icons.point_of_sale_outlined),
            activeIcon: const Icon(Icons.point_of_sale),
            label: theme.t('nav.pos'),
          ),
          BottomNavigationBarItem(
            icon: const Icon(Icons.inventory_2_outlined),
            activeIcon: const Icon(Icons.inventory_2),
            label: theme.t('nav.products'),
          ),
          BottomNavigationBarItem(
            icon: const Icon(Icons.bar_chart_outlined),
            activeIcon: const Icon(Icons.bar_chart),
            label: theme.t('nav.reports'),
          ),
          BottomNavigationBarItem(
            icon: const Icon(Icons.settings_outlined),
            activeIcon: const Icon(Icons.settings),
            label: theme.t('nav.settings'),
          ),
        ],
      ),
    );
  }
}

class _DashboardHome extends StatelessWidget {
  const _DashboardHome();

  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Hello, ${auth.user?.name.split(' ').first ?? 'User'}',
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
            ),
            Text(
              'Welcome back to your dashboard',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w400,
                color: AppColors.textSecondary,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            onPressed: () {},
            icon: const Icon(Icons.notifications_outlined),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await Future.delayed(const Duration(seconds: 1));
        },
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Balance Cards
              Row(
                children: [
                  Expanded(
                    child: _BalanceCard(
                      title: theme.t('dashboard.cashBalance'),
                      amount: 1250000,
                      icon: Icons.payments_outlined,
                      gradient: AppColors.successGradient,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _BalanceCard(
                      title: theme.t('dashboard.bankBalance'),
                      amount: 3400000,
                      icon: Icons.account_balance_outlined,
                      gradient: const [AppColors.info, Color(0xFF2563EB)],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _BalanceCard(
                title: theme.t('dashboard.mobileMoney'),
                amount: 890000,
                icon: Icons.phone_iphone_outlined,
                gradient: const [Color(0xFF8B5CF6), Color(0xFF7C3AED)],
                fullWidth: true,
              ),
              const SizedBox(height: 24),
              // Stats Grid
              GridView.count(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisCount: 2,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
                childAspectRatio: 1.1,
                children: [
                  StatCard(
                    title: theme.t('dashboard.todaySales'),
                    value: FormatUtils.currencyShort(450000),
                    icon: Icons.trending_up,
                    iconColor: AppColors.success,
                  ),
                  StatCard(
                    title: theme.t('dashboard.totalProducts'),
                    value: '248',
                    icon: Icons.inventory_2_outlined,
                    iconColor: AppColors.info,
                  ),
                  StatCard(
                    title: theme.t('dashboard.lowStock'),
                    value: '12',
                    icon: Icons.warning_amber_outlined,
                    iconColor: AppColors.warning,
                  ),
                  StatCard(
                    title: theme.t('dashboard.customers'),
                    value: '86',
                    icon: Icons.people_outlined,
                    iconColor: const Color(0xFF8B5CF6),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              // Recent Sales
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    theme.t('dashboard.recentSales'),
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  TextButton(
                    onPressed: () {},
                    child: const Text('See All'),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: 5,
                itemBuilder: (context, index) {
                  return _SaleTile(
                    receiptNo: 'RCP-${1001 + index}',
                    amount: 15000 + (index * 7500),
                    method: index % 2 == 0 ? 'Cash' : 'M-Pesa',
                    time: '${index + 1}h ago',
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _BalanceCard extends StatelessWidget {
  final String title;
  final double amount;
  final IconData icon;
  final List<Color> gradient;
  final bool fullWidth;

  const _BalanceCard({
    required this.title,
    required this.amount,
    required this.icon,
    required this.gradient,
    this.fullWidth = false,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: fullWidth ? double.infinity : null,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: gradient,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: gradient[0].withValues(alpha: 0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Icon(icon, color: Colors.white.withValues(alpha: 0.8), size: 20),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  'Active',
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.w600,
                    color: Colors.white.withValues(alpha: 0.9),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            FormatUtils.currency(amount),
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            title,
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w500,
              color: Colors.white.withValues(alpha: 0.8),
            ),
          ),
        ],
      ),
    );
  }
}

class _SaleTile extends StatelessWidget {
  final String receiptNo;
  final double amount;
  final String method;
  final String time;

  const _SaleTile({
    required this.receiptNo,
    required this.amount,
    required this.method,
    required this.time,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.divider),
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: AppColors.success.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(Icons.receipt_long, color: AppColors.success, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  receiptNo,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                Text(
                  '$method - $time',
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          Text(
            FormatUtils.currency(amount),
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: AppColors.success,
            ),
          ),
        ],
      ),
    );
  }
}

class _PlaceholderPage extends StatelessWidget {
  final String title;
  final IconData icon;

  const _PlaceholderPage({required this.title, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: EmptyState(
        icon: icon,
        title: '$title Coming Soon',
        subtitle: 'This module is under active development',
      ),
    );
  }
}
