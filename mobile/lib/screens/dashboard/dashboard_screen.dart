import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/theme_provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../utils/format_utils.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/loading_widget.dart';
import '../pos/pos_screen.dart';
import '../products/products_screen.dart';
import '../reports/reports_screen.dart';
import '../settings/settings_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _currentIndex = 0;

  late final List<Widget> _pages;

  @override
  void initState() {
    super.initState();
    _pages = [
      const _DashboardHome(),
      const PosScreen(),
      const ProductsScreen(),
      const ReportsScreen(),
      const SettingsScreen(),
    ];
  }

  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();

    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _pages,
      ),
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

class _DashboardHome extends StatefulWidget {
  const _DashboardHome();

  @override
  State<_DashboardHome> createState() => _DashboardHomeState();
}

class _DashboardHomeState extends State<_DashboardHome> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().fetchDashboard();
    });
  }

  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();
    final auth = context.watch<AuthProvider>();
    final dash = context.watch<DashboardProvider>();

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
      body: dash.isLoading
          ? const LoadingWidget(message: 'Loading dashboard...')
          : RefreshIndicator(
              onRefresh: () => dash.refresh(),
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _BalanceRow(dash: dash, theme: theme),
                    const SizedBox(height: 24),
                    _KpiGrid(dash: dash, theme: theme),
                    const SizedBox(height: 24),
                    _SalesChartCard(dash: dash),
                    const SizedBox(height: 24),
                    _TopProductsCard(dash: dash),
                    const SizedBox(height: 24),
                    _RecentSalesCard(dash: dash, theme: theme),
                  ],
                ),
              ),
            ),
    );
  }
}

class _BalanceRow extends StatelessWidget {
  final dynamic dash;
  final dynamic theme;

  const _BalanceRow({required this.dash, required this.theme});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null) return const SizedBox.shrink();

    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _BalanceCard(
                title: 'Cash Balance',
                amount: d.cashBalance,
                icon: Icons.payments_outlined,
                gradient: AppColors.successGradient,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _BalanceCard(
                title: 'Bank Balance',
                amount: d.bankBalance,
                icon: Icons.account_balance_outlined,
                gradient: const [AppColors.info, Color(0xFF2563EB)],
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        _BalanceCard(
          title: 'Mobile Money',
          amount: d.mobileBalance,
          icon: Icons.phone_iphone_outlined,
          gradient: const [Color(0xFF8B5CF6), Color(0xFF7C3AED)],
          fullWidth: true,
        ),
      ],
    );
  }
}

class _KpiGrid extends StatelessWidget {
  final dynamic dash;
  final dynamic theme;

  const _KpiGrid({required this.dash, required this.theme});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null) return const SizedBox.shrink();

    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 1.1,
      children: [
        StatCard(
          title: 'Today Sales',
          value: FormatUtils.currencyShort(d.todaySales),
          icon: Icons.trending_up,
          iconColor: AppColors.success,
        ),
        StatCard(
          title: 'Month Sales',
          value: FormatUtils.currencyShort(d.monthSales),
          icon: Icons.calendar_today_outlined,
          iconColor: AppColors.primary,
        ),
        StatCard(
          title: 'Total Products',
          value: '${d.totalProducts}',
          icon: Icons.inventory_2_outlined,
          iconColor: AppColors.info,
        ),
        StatCard(
          title: 'Low Stock',
          value: '${d.lowStockCount}',
          icon: Icons.warning_amber_outlined,
          iconColor: AppColors.warning,
        ),
        StatCard(
          title: 'Customers',
          value: '${d.totalCustomers}',
          icon: Icons.people_outlined,
          iconColor: const Color(0xFF8B5CF6),
        ),
        StatCard(
          title: 'Total Balance',
          value: FormatUtils.currencyShort(d.cashBalance + d.bankBalance + d.mobileBalance),
          icon: Icons.account_balance_wallet_outlined,
          iconColor: AppColors.gold,
        ),
      ],
    );
  }
}

class _SalesChartCard extends StatelessWidget {
  final dynamic dash;

  const _SalesChartCard({required this.dash});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null || d.salesChart.isEmpty) return const SizedBox.shrink();

    final chartData = d.salesChart as List;
    final maxVal = chartData.fold<double>(0, (prev, e) => e.value > prev ? e.value : prev);

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.divider),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withValues(alpha: 0.04),
            blurRadius: 20,
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
              const Text(
                'Weekly Sales',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  color: AppColors.textPrimary,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.success.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.trending_up, size: 14, color: AppColors.success),
                    const SizedBox(width: 4),
                    Text(
                      '+12.5%',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        color: AppColors.success,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          SizedBox(
            height: 180,
            child: _BarChart(chartData: chartData, maxVal: maxVal),
          ),
        ],
      ),
    );
  }
}

class _BarChart extends StatelessWidget {
  final List chartData;
  final double maxVal;

  const _BarChart({required this.chartData, required this.maxVal});

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final barWidth = (constraints.maxWidth - (chartData.length - 1) * 12) / chartData.length;
        return Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: chartData.map<Widget>((item) {
            final h = (item.value / maxVal) * (constraints.maxHeight - 30);
            return Column(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Container(
                  width: barWidth.clamp(20, 40),
                  height: h.clamp(8, constraints.maxHeight - 30),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [AppColors.primaryLight, AppColors.primary],
                    ),
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  item.label,
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            );
          }).toList(),
        );
      },
    );
  }
}

class _TopProductsCard extends StatelessWidget {
  final dynamic dash;

  const _TopProductsCard({required this.dash});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null || d.topProducts.isEmpty) return const SizedBox.shrink();

    final products = d.topProducts as List;
    final maxRev = products.fold<double>(0, (prev, e) => e.revenue > prev ? e.revenue : prev);

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.divider),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Top Products',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w800,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          ...products.take(5).map((p) => Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: Row(
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: AppColors.gold.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.star, color: AppColors.gold, size: 18),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        p.name,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 4),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                          value: p.revenue / maxRev,
                          minHeight: 6,
                          backgroundColor: AppColors.divider,
                          valueColor: const AlwaysStoppedAnimation(AppColors.gold),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      '${p.qtySold} sold',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    Text(
                      FormatUtils.currencyShort(p.revenue),
                      style: const TextStyle(
                        fontSize: 11,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          )),
        ],
      ),
    );
  }
}

class _RecentSalesCard extends StatelessWidget {
  final dynamic dash;
  final dynamic theme;

  const _RecentSalesCard({required this.dash, required this.theme});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null || d.recentSales.isEmpty) return const SizedBox.shrink();

    final sales = d.recentSales as List;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Recent Sales',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
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
        ...sales.take(5).map((s) => _SaleTile(
          receiptNo: s.receiptNo,
          amount: s.total,
          method: s.paymentMethod,
          time: s.date != null ? FormatUtils.relativeTime(s.date) : '',
        )),
      ],
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
    final isCash = method.toLowerCase() == 'cash';
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
              color: (isCash ? AppColors.success : AppColors.info).withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(
              isCash ? Icons.payments : Icons.phone_iphone,
              color: isCash ? AppColors.success : AppColors.info,
              size: 20,
            ),
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
                  '${isCash ? 'Cash' : 'M-Pesa'} - $time',
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
