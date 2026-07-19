import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../utils/format_utils.dart';
import '../../widgets/loading_widget.dart';
import '../pos/pos_screen.dart';
import '../products/products_screen.dart';
import '../reports/reports_screen.dart';
import '../sales/sales_screen.dart';
import '../customers/customers_screen.dart';
import '../more/more_screen.dart';
import '../parties/add_party_screen.dart';
import '../sales/add_sale_screen.dart';
import '../products/add_item_screen.dart';
import '../quick_entry/quick_entry_screen.dart';
import '../notebook/notebook_screen.dart';
import '../transactions/add_payment_screen.dart';
import '../transactions/add_purchase_screen.dart';
import '../transactions/add_expense_screen.dart';
import '../transactions/add_income_screen.dart';
import '../transactions/add_reminder_screen.dart';
import '../transactions/stock_adjustment_screen.dart';
import 'edit_shortcuts_screen.dart';
import 'kpi_detail_screen.dart';
import '../../services/shortcuts_store.dart';
import '../../services/notification_service.dart';
import '../../services/sale_watcher_service.dart';
import '../auth/business_setup_screen.dart';
import '../settings/notification_settings_screen.dart';

/// Root shell for the signed-in app. Bottom navigation mirrors the
/// reference design: Home, Transactions, Parties, Inventory, More.
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
    _pages = const [
      _HomeTab(),
      SalesScreen(),
      CustomersScreen(),
      ProductsScreen(),
      MoreScreen(),
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _pages,
      ),
      floatingActionButton: _currentIndex == 0
          ? FloatingActionButton(
              backgroundColor: AppColors.gold,
              foregroundColor: Colors.white,
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PosScreen())),
              child: const Icon(Icons.point_of_sale),
            )
          : null,
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (i) => setState(() => _currentIndex = i),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), activeIcon: Icon(Icons.receipt_long), label: 'Transactions'),
          BottomNavigationBarItem(icon: Icon(Icons.people_outline), activeIcon: Icon(Icons.people), label: 'Parties'),
          BottomNavigationBarItem(icon: Icon(Icons.inventory_2_outlined), activeIcon: Icon(Icons.inventory_2), label: 'Inventory'),
          BottomNavigationBarItem(icon: Icon(Icons.apps_outlined), activeIcon: Icon(Icons.apps), label: 'More'),
        ],
      ),
    );
  }
}

class _HomeTab extends StatefulWidget {
  const _HomeTab();

  @override
  State<_HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<_HomeTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      context.read<DashboardProvider>().fetchDashboard();
      // Ask for notification permission once the user is actually looking
      // at something useful, then start watching for sales made on other
      // devices/by other staff.
      await NotificationService.instance.requestPermission();
      SaleWatcherService.instance.start();
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final dash = context.watch<DashboardProvider>();
    final name = auth.user?.name ?? 'User';
    final role = auth.user?.role ?? 'user';
    final monthLabel = FormatUtils.monthYear(DateTime.now()).split(' ').first;

    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        titleSpacing: 16,
        title: Row(
          children: [
            CircleAvatar(
              radius: 18,
              backgroundColor: AppColors.primary.withValues(alpha: 0.1),
              child: Text(
                name.isNotEmpty ? name[0].toUpperCase() : 'U',
                style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary),
              ),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(name, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                Text(
                  role.isNotEmpty ? role[0].toUpperCase() + role.substring(1) : 'User',
                  style: const TextStyle(fontSize: 11, color: AppColors.textSecondary, fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationSettingsScreen())),
            icon: const Icon(Icons.notifications_outlined),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: dash.isLoading
          ? const LoadingWidget(message: 'Loading dashboard...')
          : dash.data == null
              ? _DashboardErrorState(dash: dash)
              : RefreshIndicator(
              onRefresh: () => dash.refresh(),
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _KpiSection(dash: dash, monthLabel: monthLabel),
                    const SizedBox(height: 24),
                    const _SectionHeading(title: 'Explore App'),
                    const SizedBox(height: 12),
                    _ExploreAppRow(),
                    const SizedBox(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const _SectionHeading(title: 'Shortcuts'),
                        TextButton.icon(
                          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const EditShortcutsScreen())),
                          icon: const Icon(Icons.edit_outlined, size: 16),
                          label: const Text('Edit Menu'),
                          style: TextButton.styleFrom(foregroundColor: AppColors.primary, padding: EdgeInsets.zero),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    const _ShortcutsGrid(),
                    const SizedBox(height: 24),
                    _CashflowCard(dash: dash),
                    if (dash.data != null && dash.data!.topProducts.isNotEmpty) ...[
                      const SizedBox(height: 24),
                      const _SectionHeading(title: 'Top Products'),
                      const SizedBox(height: 12),
                      _TopProductsCard(dash: dash),
                    ],
                    if (dash.data != null && dash.data!.recentSales.isNotEmpty) ...[
                      const SizedBox(height: 24),
                      const _SectionHeading(title: 'Recent Sales'),
                      const SizedBox(height: 12),
                      _RecentSalesCard(dash: dash),
                    ],
                  ],
                ),
              ),
            ),
    );
  }
}

/// Shown instead of the dashboard body when [DashboardProvider] couldn't
/// load real data — never falls back to fake numbers. Two distinct cases:
/// the user has no business yet (route to setup), or a genuine load
/// failure (offer retry).
class _DashboardErrorState extends StatelessWidget {
  final DashboardProvider dash;
  const _DashboardErrorState({required this.dash});

  @override
  Widget build(BuildContext context) {
    final needsSetup = dash.needsBusinessSetup;
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: (needsSetup ? AppColors.gold : AppColors.error).withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(
                needsSetup ? Icons.store_outlined : Icons.cloud_off_outlined,
                color: needsSetup ? AppColors.gold : AppColors.error,
                size: 32,
              ),
            ),
            const SizedBox(height: 20),
            Text(
              needsSetup ? 'Set up your business to continue' : 'Couldn\'t load your dashboard',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
            ),
            const SizedBox(height: 8),
            Text(
              needsSetup
                  ? 'You\'re signed in, but there\'s no business linked to your account yet. Finish setup to start seeing your real sales, stock and reports.'
                  : (dash.error ?? 'Something went wrong. Please try again.'),
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 13, color: AppColors.textSecondary, height: 1.4),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: () async {
                if (needsSetup) {
                  await Navigator.push(context, MaterialPageRoute(builder: (_) => const BusinessSetupScreen()));
                  if (context.mounted) context.read<DashboardProvider>().fetchDashboard();
                } else {
                  context.read<DashboardProvider>().fetchDashboard();
                }
              },
              icon: Icon(needsSetup ? Icons.arrow_forward : Icons.refresh),
              label: Text(needsSetup ? 'Set Up Business' : 'Try Again'),
              style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14)),
            ),
          ],
        ),
      ),
    );
  }
}

class _SectionHeading extends StatelessWidget {
  final String title;
  const _SectionHeading({required this.title});

  @override
  Widget build(BuildContext context) {
    return Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.textPrimary));
  }
}

/// The three rows of KPI cards: To Receive / To Give, Sales / Purchase
/// (this month), Expense (this month) / Total Balance.
class _KpiSection extends StatelessWidget {
  final DashboardProvider dash;
  final String monthLabel;

  const _KpiSection({required this.dash, required this.monthLabel});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null) return const SizedBox.shrink();

    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _FlatKpiCard(
                label: 'To Receive',
                value: FormatUtils.currencyShort(d.toReceive),
                icon: Icons.arrow_downward,
                bg: AppColors.successLight,
                fg: const Color(0xFF047857),
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'To Receive',
                  valueLabel: FormatUtils.currency(d.toReceive),
                  color: const Color(0xFF047857),
                  icon: Icons.arrow_downward,
                  emptyHint: 'This is the total your customers currently owe you. Open the Parties tab and filter "To Receive" to see who owes what.',
                ))),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _FlatKpiCard(
                label: 'To Give',
                value: FormatUtils.currencyShort(d.toGive),
                icon: Icons.arrow_upward,
                bg: AppColors.errorLight,
                fg: const Color(0xFFB91C1C),
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'To Give',
                  valueLabel: FormatUtils.currency(d.toGive),
                  color: const Color(0xFFB91C1C),
                  icon: Icons.arrow_upward,
                  emptyHint: 'This is the total you currently owe your suppliers. Open the Parties tab and filter "To Give" to see the full list.',
                ))),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _FlatKpiCard(
                label: 'Sales ($monthLabel)',
                value: FormatUtils.currencyShort(d.monthSales),
                icon: Icons.point_of_sale_outlined,
                bg: context.cardBg,
                fg: AppColors.primary,
                bordered: true,
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'Sales ($monthLabel)',
                  valueLabel: FormatUtils.currency(d.monthSales),
                  color: AppColors.primary,
                  icon: Icons.point_of_sale_outlined,
                  series: d.salesChart,
                  seriesCaption: 'Sales — Last 7 Days',
                ))),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _FlatKpiCard(
                label: 'Purchase ($monthLabel)',
                value: FormatUtils.currencyShort(d.monthPurchases),
                icon: Icons.shopping_bag_outlined,
                bg: context.cardBg,
                fg: AppColors.info,
                bordered: true,
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'Purchase ($monthLabel)',
                  valueLabel: FormatUtils.currency(d.monthPurchases),
                  color: AppColors.info,
                  icon: Icons.shopping_bag_outlined,
                  series: d.purchasesChart,
                  seriesCaption: 'Purchases — Last 7 Days',
                ))),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: _FlatKpiCard(
                label: 'Expense ($monthLabel)',
                value: FormatUtils.currencyShort(d.monthExpenses),
                icon: Icons.receipt_outlined,
                bg: context.cardBg,
                fg: AppColors.error,
                bordered: true,
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'Expense ($monthLabel)',
                  valueLabel: FormatUtils.currency(d.monthExpenses),
                  color: AppColors.error,
                  icon: Icons.receipt_outlined,
                  series: d.expensesChart,
                  seriesCaption: 'Expenses — Last 7 Days',
                ))),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _FlatKpiCard(
                label: 'Total Balance',
                value: FormatUtils.currencyShort(d.totalBalance),
                sub: 'Cash & Bank',
                icon: Icons.account_balance_wallet_outlined,
                bg: context.cardBg,
                fg: AppColors.gold,
                bordered: true,
                onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => KpiDetailScreen(
                  title: 'Total Balance',
                  valueLabel: FormatUtils.currency(d.totalBalance),
                  color: AppColors.goldDark,
                  icon: Icons.account_balance_wallet_outlined,
                  breakdown: [
                    ('Cash', d.cashBalance, AppColors.success),
                    ('Bank', d.bankBalance, AppColors.info),
                    ('Mobile Money', d.mobileBalance, AppColors.gold),
                  ],
                ))),
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _FlatKpiCard extends StatelessWidget {
  final String label;
  final String value;
  final String? sub;
  final IconData icon;
  final Color bg;
  final Color fg;
  final bool bordered;
  final VoidCallback? onTap;

  const _FlatKpiCard({
    required this.label,
    required this.value,
    this.sub,
    required this.icon,
    required this.bg,
    required this.fg,
    this.bordered = false,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: bg,
          borderRadius: BorderRadius.circular(14),
          border: bordered ? Border.all(color: context.borderColor) : null,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(child: Text(value, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: fg), overflow: TextOverflow.ellipsis)),
                Icon(icon, size: 16, color: fg),
              ],
            ),
            const SizedBox(height: 6),
            Row(
              children: [
                Expanded(
                  child: Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                ),
                Icon(Icons.chevron_right, size: 14, color: fg.withValues(alpha: 0.5)),
              ],
            ),
            if (sub != null)
              Text(sub!, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w500, color: AppColors.textHint)),
          ],
        ),
      ),
    );
  }
}

class _ExploreAppRow extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: _exploreTile(
            context,
            icon: Icons.flash_on_rounded,
            label: 'Quick Entry',
            colors: const [Color(0xFFF9AC00), Color(0xFFFFC64D)],
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const QuickEntryScreen())),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _exploreTile(
            context,
            icon: Icons.point_of_sale_rounded,
            label: 'Quick POS',
            colors: const [AppColors.primary, AppColors.primaryLight],
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PosScreen())),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _exploreTile(
            context,
            icon: Icons.bar_chart_rounded,
            label: 'View Reports',
            colors: const [Color(0xFF3B82F6), Color(0xFF60A5FA)],
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ReportsScreen())),
          ),
        ),
      ],
    );
  }

  Widget _exploreTile(
    BuildContext context, {
    required IconData icon,
    required String label,
    required List<Color> colors,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(18),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(
          color: context.cardBg,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: context.borderColor),
          boxShadow: [
            BoxShadow(color: colors.first.withValues(alpha: 0.10), blurRadius: 14, offset: const Offset(0, 6)),
          ],
        ),
        child: Column(
          children: [
            Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: colors, begin: Alignment.topLeft, end: Alignment.bottomRight),
                borderRadius: BorderRadius.circular(14),
                boxShadow: [BoxShadow(color: colors.first.withValues(alpha: 0.35), blurRadius: 10, offset: const Offset(0, 4))],
              ),
              child: Icon(icon, color: Colors.white, size: 22),
            ),
            const SizedBox(height: 10),
            Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w800)),
          ],
        ),
      ),
    );
  }
}

class _ShortcutsGrid extends StatelessWidget {
  const _ShortcutsGrid();

  void _handleTap(BuildContext context, String label) {
    switch (label) {
      case 'Add Party':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddPartyScreen()));
        break;
      case 'Sales Invoice':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddSaleScreen()));
        break;
      case 'Payment In':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddPaymentScreen(isIn: true)));
        break;
      case 'Payment Out':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddPaymentScreen(isIn: false)));
        break;
      case 'Purchase':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddPurchaseScreen()));
        break;
      case 'Add Item':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddItemScreen()));
        break;
      case 'Expense':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddExpenseScreen()));
        break;
      case 'Other Income':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddIncomeScreen()));
        break;
      case 'Add Note':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const NotebookScreen(autoAdd: true)));
        break;
      case 'Stock Adjustment':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const StockAdjustmentScreen()));
        break;
      case 'Add Reminder':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const AddReminderScreen()));
        break;
      case 'Sales Return':
      case 'Purchase Return':
      case 'Quotation':
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$label is on our roadmap — not wired up yet.'), behavior: SnackBarBehavior.floating),
        );
        break;
      default:
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('$label — coming soon'), behavior: SnackBarBehavior.floating),
        );
    }
  }

  @override
  Widget build(BuildContext context) {
    return ListenableBuilder(
      listenable: ShortcutsStore.instance,
      builder: (context, _) {
        final items = ShortcutsStore.instance.visible;
        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: context.cardBg,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: context.borderColor),
          ),
          child: GridView.count(
            crossAxisCount: 4,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 16,
            crossAxisSpacing: 8,
            childAspectRatio: 0.8,
            children: items.map((item) {
              return InkWell(
                onTap: () => _handleTap(context, item.label),
                borderRadius: BorderRadius.circular(12),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(12)),
                      child: Icon(item.icon, color: AppColors.primary, size: 20),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      item.label,
                      textAlign: TextAlign.center,
                      style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: AppColors.textSecondary, height: 1.2),
                    ),
                  ],
                ),
              );
            }).toList(),
          ),
        );
      },
    );
  }
}

class _CashflowCard extends StatefulWidget {
  final DashboardProvider dash;
  const _CashflowCard({required this.dash});

  @override
  State<_CashflowCard> createState() => _CashflowCardState();
}

class _CashflowCardState extends State<_CashflowCard> {
  String _period = 'Daily';

  @override
  Widget build(BuildContext context) {
    final d = widget.dash.data;
    if (d == null || d.cashflowIn.isEmpty) return const SizedBox.shrink();

    final totalIn = d.cashflowIn.fold<double>(0, (p, e) => p + e.value);
    final totalOut = d.cashflowOut.fold<double>(0, (p, e) => p + e.value);
    final maxVal = [
      ...d.cashflowIn.map((e) => e.value),
      ...d.cashflowOut.map((e) => e.value),
    ].fold<double>(0, (p, v) => v > p ? v : p);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: context.cardBg,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Cashflow (Last 7 Days)', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
              DropdownButton<String>(
                value: _period,
                underline: const SizedBox.shrink(),
                items: const [
                  DropdownMenuItem(value: 'Daily', child: Text('Daily', style: TextStyle(fontSize: 12))),
                  DropdownMenuItem(value: 'Weekly', child: Text('Weekly', style: TextStyle(fontSize: 12))),
                ],
                onChanged: (v) => setState(() => _period = v ?? 'Daily'),
              ),
            ],
          ),
          const SizedBox(height: 16),
          SizedBox(
            height: 160,
            child: _DualBarChart(inData: d.cashflowIn, outData: d.cashflowOut, maxVal: maxVal),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              _legend('Total Money In', AppColors.success, FormatUtils.currency(totalIn)),
              const SizedBox(width: 24),
              _legend('Total Money Out', AppColors.error, FormatUtils.currency(totalOut)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _legend(String label, Color color, String value) {
    return Expanded(
      child: Row(
        children: [
          Container(width: 10, height: 10, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
          const SizedBox(width: 6),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textSecondary)),
                Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800), overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DualBarChart extends StatelessWidget {
  final List<dynamic> inData;
  final List<dynamic> outData;
  final double maxVal;

  const _DualBarChart({required this.inData, required this.outData, required this.maxVal});

  @override
  Widget build(BuildContext context) {
    final safeMax = maxVal <= 0 ? 1.0 : maxVal;
    return LayoutBuilder(
      builder: (context, constraints) {
        final chartHeight = constraints.maxHeight - 24;
        return Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: List.generate(inData.length, (i) {
            final inVal = inData[i].value as double;
            final outVal = i < outData.length ? outData[i].value as double : 0.0;
            final inH = (inVal / safeMax) * chartHeight;
            final outH = (outVal / safeMax) * chartHeight;
            return Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Container(
                        width: 8,
                        height: inH.clamp(4, chartHeight),
                        margin: const EdgeInsets.symmetric(horizontal: 1.5),
                        decoration: BoxDecoration(color: AppColors.success, borderRadius: BorderRadius.circular(4)),
                      ),
                      Container(
                        width: 8,
                        height: outH.clamp(4, chartHeight),
                        margin: const EdgeInsets.symmetric(horizontal: 1.5),
                        decoration: BoxDecoration(color: AppColors.error, borderRadius: BorderRadius.circular(4)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    inData[i].label as String,
                    style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: AppColors.textSecondary),
                  ),
                ],
              ),
            );
          }),
        );
      },
    );
  }
}

class _TopProductsCard extends StatelessWidget {
  final DashboardProvider dash;

  const _TopProductsCard({required this.dash});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null || d.topProducts.isEmpty) return const SizedBox.shrink();

    final products = d.topProducts;
    final maxRev = products.fold<double>(0, (prev, e) => e.revenue > prev ? e.revenue : prev);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: context.cardBg,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: products.take(5).map((p) => Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                children: [
                  Container(
                    width: 36,
                    height: 36,
                    decoration: BoxDecoration(color: AppColors.gold.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.star, color: AppColors.gold, size: 18),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(p.name, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
                        const SizedBox(height: 4),
                        ClipRRect(
                          borderRadius: BorderRadius.circular(4),
                          child: LinearProgressIndicator(
                            value: maxRev > 0 ? p.revenue / maxRev : 0,
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
                      Text('${p.qtySold} sold', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                      Text(FormatUtils.currencyShort(p.revenue), style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                    ],
                  ),
                ],
              ),
            )).toList(),
      ),
    );
  }
}

class _RecentSalesCard extends StatelessWidget {
  final DashboardProvider dash;

  const _RecentSalesCard({required this.dash});

  @override
  Widget build(BuildContext context) {
    final d = dash.data;
    if (d == null || d.recentSales.isEmpty) return const SizedBox.shrink();

    return Column(
      children: d.recentSales.take(5).map((s) => _SaleTile(
            receiptNo: s.receiptNo,
            amount: s.total,
            method: s.paymentMethod,
            time: s.date != null ? FormatUtils.relativeTime(s.date!) : '',
          )).toList(),
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
        color: context.cardBg,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor),
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
            child: Icon(isCash ? Icons.payments : Icons.phone_iphone, color: isCash ? AppColors.success : AppColors.info, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(receiptNo, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                Text('${isCash ? 'Cash' : 'M-Pesa'} - $time', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Text(FormatUtils.currency(amount), style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.success)),
        ],
      ),
    );
  }
}
