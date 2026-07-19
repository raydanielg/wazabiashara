import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';

class ReportsScreen extends StatefulWidget {
  const ReportsScreen({super.key});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  String _period = 'week';

  final Map<String, List<double>> _salesData = {
    'week': [320000, 450000, 380000, 520000, 680000, 750000, 420000],
    'month': [1200000, 1800000, 1500000, 2100000],
    'year': [9500000, 11200000, 10800000, 13500000, 12200000, 14000000, 11800000, 13200000, 12500000, 15000000, 13800000, 16500000],
  };

  final Map<String, List<String>> _labels = {
    'week': ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    'month': ['W1', 'W2', 'W3', 'W4'],
    'year': ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  };

  List<double> get _data => _salesData[_period]!;
  List<String> get _labels => _labels[_period]!;

  double get _total => _data.fold(0, (a, b) => a + b);
  double get _avg => _total / _data.length;
  double get _max => _data.fold(0, (a, b) => b > a ? b : a);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Reports')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _periodSelector(),
            const SizedBox(height: 24),
            _summaryRow(),
            const SizedBox(height: 24),
            _chartCard(),
            const SizedBox(height: 24),
            _paymentBreakdown(),
            const SizedBox(height: 24),
            _topCategories(),
          ],
        ),
      ),
    );
  }

  Widget _periodSelector() {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.divider),
      ),
      child: Row(
        children: [
          _periodBtn('week', 'Week'),
          _periodBtn('month', 'Month'),
          _periodBtn('year', 'Year'),
        ],
      ),
    );
  }

  Widget _periodBtn(String value, String label) {
    final selected = _period == value;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _period = value),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Center(
            child: Text(label, style: TextStyle(
              fontWeight: FontWeight.w700,
              color: selected ? Colors.white : AppColors.textSecondary,
            )),
          ),
        ),
      ),
    );
  }

  Widget _summaryRow() {
    return Row(
      children: [
        Expanded(child: _kpiCard('Total Sales', FormatUtils.currencyShort(_total), AppColors.primary, Icons.trending_up)),
        const SizedBox(width: 12),
        Expanded(child: _kpiCard('Average', FormatUtils.currencyShort(_avg), AppColors.info, Icons.bar_chart)),
        const SizedBox(width: 12),
        Expanded(child: _kpiCard('Peak', FormatUtils.currencyShort(_max), AppColors.success, Icons.emoji_events)),
      ],
    );
  }

  Widget _kpiCard(String title, String value, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.divider),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 32, height: 32,
            decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, color: color, size: 18),
          ),
          const SizedBox(height: 10),
          Text(value, style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: color)),
          const SizedBox(height: 2),
          Text(title, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
        ],
      ),
    );
  }

  Widget _chartCard() {
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
          const Text('Sales Trend', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
          const SizedBox(height: 20),
          SizedBox(
            height: 200,
            child: LayoutBuilder(
              builder: (ctx, constraints) {
                final w = (constraints.maxWidth - (_data.length - 1) * 8) / _data.length;
                return Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: List.generate(_data.length, (i) {
                    final h = (_data[i] / _max) * (constraints.maxHeight - 28);
                    return Column(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Container(
                          width: w.clamp(12, 40),
                          height: h.clamp(6, constraints.maxHeight - 28),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                              colors: [AppColors.primaryLight, AppColors.primary],
                            ),
                            borderRadius: BorderRadius.circular(6),
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(_labels[i], style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                      ],
                    );
                  }),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _paymentBreakdown() {
    final cash = _total * 0.45;
    final mpesa = _total * 0.40;
    final bank = _total * 0.15;
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
          const Text('Payment Methods', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
          const SizedBox(height: 16),
          _paymentBar('Cash', cash, _total, AppColors.success),
          const SizedBox(height: 12),
          _paymentBar('M-Pesa', mpesa, _total, AppColors.info),
          const SizedBox(height: 12),
          _paymentBar('Bank', bank, _total, const Color(0xFF8B5CF6)),
        ],
      ),
    );
  }

  Widget _paymentBar(String label, double value, double total, Color color) {
    final pct = (value / total) * 100;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            Text('${pct.toStringAsFixed(0)}%', style: TextStyle(fontWeight: FontWeight.w800, color: color, fontSize: 13)),
          ],
        ),
        const SizedBox(height: 6),
        ClipRRect(
          borderRadius: BorderRadius.circular(6),
          child: LinearProgressIndicator(
            value: value / total,
            minHeight: 8,
            backgroundColor: AppColors.divider,
            valueColor: AlwaysStoppedAnimation(color),
          ),
        ),
        const SizedBox(height: 4),
        Text(FormatUtils.currencyShort(value), style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
      ],
    );
  }

  Widget _topCategories() {
    final cats = [
      ('Food', 4500000, AppColors.primary),
      ('Drinks', 2800000, AppColors.info),
      ('Dairy', 1500000, AppColors.success),
      ('Bakery', 900000, AppColors.gold),
      ('Household', 600000, const Color(0xFF8B5CF6)),
    ];
    final maxVal = cats.map((c) => c.$2).reduce((a, b) => a > b ? a : b);

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
          const Text('Sales by Category', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
          const SizedBox(height: 16),
          ...cats.map((c) => Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: Row(
              children: [
                Expanded(
                  flex: 3,
                  child: Text(c.$1, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                ),
                Expanded(
                  flex: 5,
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: LinearProgressIndicator(
                      value: c.$2 / maxVal,
                      minHeight: 8,
                      backgroundColor: AppColors.divider,
                      valueColor: AlwaysStoppedAnimation(c.$3),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                SizedBox(
                  width: 70,
                  child: Text(FormatUtils.currencyShort(c.$2), textAlign: TextAlign.end, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                ),
              ],
            ),
          )),
        ],
      ),
    );
  }
}
