import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';
import 'sales_report_screen.dart';

class _Point {
  final String label;
  final double value;
  _Point(this.label, this.value);
  factory _Point.fromJson(Map<String, dynamic> j) => _Point(j['label'] as String? ?? '', (j['value'] as num?)?.toDouble() ?? 0);
}

/// Reports screen — pulls real numbers from /reports/chart-data
/// (ReportController::chartData) instead of hardcoded sample data, so what's
/// shown here always reflects the signed-in business's actual sales.
class ReportsScreen extends StatefulWidget {
  const ReportsScreen({super.key});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  final _api = ApiService();
  String _period = 'week';
  bool _isLoading = true;

  List<_Point> _series = [];
  double _total = 0;
  double _avg = 0;
  double _max = 0;
  List<_Point> _paymentMethods = [];
  List<_Point> _topCategories = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getReports(period: _period);
      if (res.statusCode == 200 && res.data['success'] == true) {
        final d = res.data['data'] as Map<String, dynamic>;
        final series = (d['series'] as List?) ?? [];
        final summary = (d['summary'] as Map<String, dynamic>?) ?? {};
        final methods = (d['paymentMethods'] as List?) ?? [];
        final categories = (d['topCategories'] as List?) ?? [];
        setState(() {
          _series = series.map((e) => _Point.fromJson(e)).toList();
          _total = (summary['total'] as num?)?.toDouble() ?? 0;
          _avg = (summary['average'] as num?)?.toDouble() ?? 0;
          _max = (summary['peak'] as num?)?.toDouble() ?? 0;
          _paymentMethods = methods.map((e) => _Point.fromJson(e)).toList();
          _topCategories = categories.map((e) => _Point.fromJson(e)).toList();
        });
      } else {
        setState(() {
          _series = [];
          _total = 0;
          _avg = 0;
          _max = 0;
          _paymentMethods = [];
          _topCategories = [];
        });
      }
    } catch (_) {
      // No connectivity — show a genuine empty state rather than fake numbers.
      setState(() {
        _series = [];
        _total = 0;
        _avg = 0;
        _max = 0;
        _paymentMethods = [];
        _topCategories = [];
      });
    }
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Reports')),
      body: _isLoading
          ? const LoadingWidget(message: 'Loading report...')
          : RefreshIndicator(
              onRefresh: _load,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _periodSelector(),
                    const SizedBox(height: 24),
                    _summaryRow(),
                    const SizedBox(height: 24),
                    _chartCard(),
                    if (_paymentMethods.isNotEmpty) ...[
                      const SizedBox(height: 24),
                      _paymentBreakdown(),
                    ],
                    if (_topCategories.isNotEmpty) ...[
                      const SizedBox(height: 24),
                      _topCategoriesCard(),
                    ],
                  ],
                ),
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
        onTap: () {
          if (_period == value) return;
          setState(() => _period = value);
          _load();
        },
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
        Expanded(child: _kpiCard('Total Sales', FormatUtils.currencyShort(_total), AppColors.primary, Icons.trending_up, onTap: () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => const SalesReportScreen()));
        })),
        const SizedBox(width: 12),
        Expanded(child: _kpiCard('Average', FormatUtils.currencyShort(_avg), AppColors.info, Icons.bar_chart)),
        const SizedBox(width: 12),
        Expanded(child: _kpiCard('Peak', FormatUtils.currencyShort(_max), AppColors.success, Icons.emoji_events)),
      ],
    );
  }

  Widget _kpiCard(String title, String value, Color color, IconData icon, {VoidCallback? onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
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
            child: _series.isEmpty
                ? const Center(
                    child: Text('No sales recorded for this period yet', style: TextStyle(color: AppColors.textHint, fontSize: 12)),
                  )
                : LayoutBuilder(
                    builder: (ctx, constraints) {
                      final safeMax = _max <= 0 ? 1.0 : _max;
                      final w = (constraints.maxWidth - (_series.length - 1) * 8) / _series.length;
                      return Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: _series.map((point) {
                          final h = (point.value / safeMax) * (constraints.maxHeight - 28);
                          return Column(
                            mainAxisAlignment: MainAxisAlignment.end,
                            children: [
                              Container(
                                width: w.clamp(12, 40),
                                height: h.clamp(4, constraints.maxHeight - 28),
                                decoration: BoxDecoration(
                                  gradient: const LinearGradient(
                                    begin: Alignment.topCenter,
                                    end: Alignment.bottomCenter,
                                    colors: [AppColors.primaryLight, AppColors.primary],
                                  ),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(point.label, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                            ],
                          );
                        }).toList(),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }

  Widget _paymentBreakdown() {
    final total = _paymentMethods.fold<double>(0, (p, e) => p + e.value);
    final colors = [AppColors.success, AppColors.info, const Color(0xFF8B5CF6), AppColors.gold, AppColors.error];
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
          ..._paymentMethods.asMap().entries.map((entry) => Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: _paymentBar(entry.value.label, entry.value.value, total, colors[entry.key % colors.length]),
              )),
        ],
      ),
    );
  }

  Widget _paymentBar(String label, double value, double total, Color color) {
    final pct = total > 0 ? (value / total) * 100 : 0.0;
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
            value: total > 0 ? value / total : 0,
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

  Widget _topCategoriesCard() {
    final colors = [AppColors.primary, AppColors.info, AppColors.success, AppColors.gold, const Color(0xFF8B5CF6), AppColors.error];
    final maxVal = _topCategories.fold<double>(0, (p, e) => e.value > p ? e.value : p);

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
          ..._topCategories.asMap().entries.map((entry) => Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: Row(
              children: [
                Expanded(
                  flex: 3,
                  child: Text(entry.value.label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13), overflow: TextOverflow.ellipsis),
                ),
                Expanded(
                  flex: 5,
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: LinearProgressIndicator(
                      value: maxVal > 0 ? entry.value.value / maxVal : 0,
                      minHeight: 8,
                      backgroundColor: AppColors.divider,
                      valueColor: AlwaysStoppedAnimation(colors[entry.key % colors.length]),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                SizedBox(
                  width: 70,
                  child: Text(FormatUtils.currencyShort(entry.value.value), textAlign: TextAlign.end, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                ),
              ],
            ),
          )),
        ],
      ),
    );
  }
}
