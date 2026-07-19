import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';
import '../../models/dashboard_data.dart';

/// A polished, tappable detail view for a single Home-screen KPI card.
/// Tapping a KPI on the dashboard "opens" its own chart — either a smooth
/// fl_chart line trend (Sales / Purchase / Expense) or a breakdown bar
/// (Total Balance), with an honest empty state where no daily history
/// exists yet (To Receive / To Give).
class KpiDetailScreen extends StatelessWidget {
  final String title;
  final String valueLabel;
  final Color color;
  final IconData icon;
  final List<ChartData>? series;
  final String? seriesCaption;
  final List<(String, double, Color)>? breakdown;
  final String? emptyHint;

  const KpiDetailScreen({
    super.key,
    required this.title,
    required this.valueLabel,
    required this.color,
    required this.icon,
    this.series,
    this.seriesCaption,
    this.breakdown,
    this.emptyHint,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _headerCard(),
            const SizedBox(height: 20),
            if (series != null && series!.isNotEmpty)
              _lineChartCard()
            else if (breakdown != null && breakdown!.isNotEmpty)
              _breakdownCard()
            else
              _emptyCard(),
          ],
        ),
      ),
    );
  }

  Widget _headerCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color.withValues(alpha: 0.16), color.withValues(alpha: 0.02)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withValues(alpha: 0.25)),
      ),
      child: Row(
        children: [
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(color: color.withValues(alpha: 0.16), borderRadius: BorderRadius.circular(16)),
            child: Icon(icon, color: color, size: 26),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(valueLabel, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: color)),
                const SizedBox(height: 4),
                Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _lineChartCard() {
    final s = series!;
    final rawMax = s.map((e) => e.value).fold<double>(0, (p, v) => v > p ? v : p);
    final safeMax = rawMax <= 0 ? 1.0 : rawMax * 1.25;
    final rawMin = s.map((e) => e.value).fold<double>(rawMax, (p, v) => v < p ? v : p);
    final avg = s.fold<double>(0, (p, e) => p + e.value) / s.length;

    return Container(
      padding: const EdgeInsets.fromLTRB(4, 20, 20, 16),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(20), border: Border.all(color: AppColors.divider)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(left: 16),
            child: Text(seriesCaption ?? 'Trend', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
          ),
          const SizedBox(height: 20),
          SizedBox(
            height: 220,
            child: LineChart(
              LineChartData(
                minY: 0,
                maxY: safeMax,
                minX: 0,
                maxX: (s.length - 1).toDouble(),
                gridData: FlGridData(
                  show: true,
                  drawVerticalLine: false,
                  horizontalInterval: safeMax / 4,
                  getDrawingHorizontalLine: (v) => FlLine(color: AppColors.divider, strokeWidth: 1),
                ),
                borderData: FlBorderData(show: false),
                titlesData: FlTitlesData(
                  topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 46,
                      interval: safeMax / 4,
                      getTitlesWidget: (v, meta) => Padding(
                        padding: const EdgeInsets.only(right: 4),
                        child: Text(FormatUtils.currencyShort(v), style: const TextStyle(fontSize: 9, color: AppColors.textHint)),
                      ),
                    ),
                  ),
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 26,
                      getTitlesWidget: (v, meta) {
                        final i = v.toInt();
                        if (i < 0 || i >= s.length) return const SizedBox.shrink();
                        return Padding(
                          padding: const EdgeInsets.only(top: 6),
                          child: Text(s[i].label, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                        );
                      },
                    ),
                  ),
                ),
                lineTouchData: LineTouchData(
                  touchTooltipData: LineTouchTooltipData(
                    getTooltipColor: (_) => AppColors.textPrimary,
                    getTooltipItems: (spots) => spots
                        .map((sp) => LineTooltipItem(
                              FormatUtils.currency(sp.y),
                              const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 11),
                            ))
                        .toList(),
                  ),
                ),
                lineBarsData: [
                  LineChartBarData(
                    spots: [for (int i = 0; i < s.length; i++) FlSpot(i.toDouble(), s[i].value)],
                    isCurved: true,
                    curveSmoothness: 0.28,
                    color: color,
                    barWidth: 3,
                    isStrokeCapRound: true,
                    dotData: FlDotData(
                      show: true,
                      getDotPainter: (spot, percent, bar, index) =>
                          FlDotCirclePainter(radius: 3.2, color: color, strokeWidth: 2, strokeColor: Colors.white),
                    ),
                    belowBarData: BarAreaData(
                      show: true,
                      gradient: LinearGradient(
                        colors: [color.withValues(alpha: 0.28), color.withValues(alpha: 0.0)],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              _statChip('Low', FormatUtils.currencyShort(rawMin), AppColors.textSecondary),
              const SizedBox(width: 10),
              _statChip('Average', FormatUtils.currencyShort(avg), color),
              const SizedBox(width: 10),
              _statChip('Peak', FormatUtils.currencyShort(rawMax), AppColors.success),
            ],
          ),
        ],
      ),
    );
  }

  Widget _statChip(String label, String value, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(color: color.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(12)),
        child: Column(
          children: [
            Text(value, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: color)),
            const SizedBox(height: 2),
            Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }

  Widget _breakdownCard() {
    final items = breakdown!;
    final total = items.fold<double>(0, (p, e) => p + e.$2);
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(20), border: Border.all(color: AppColors.divider)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Breakdown', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
          const SizedBox(height: 18),
          ...items.map((e) {
            final pct = total > 0 ? e.$2 / total : 0.0;
            return Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(width: 10, height: 10, decoration: BoxDecoration(color: e.$3, shape: BoxShape.circle)),
                          const SizedBox(width: 8),
                          Text(e.$1, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                        ],
                      ),
                      Text(FormatUtils.currency(e.$2), style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: e.$3)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(6),
                    child: LinearProgressIndicator(value: pct, minHeight: 8, backgroundColor: AppColors.divider, valueColor: AlwaysStoppedAnimation(e.$3)),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _emptyCard() {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(20), border: Border.all(color: AppColors.divider)),
      child: Column(
        children: [
          Icon(Icons.insights_outlined, size: 40, color: color.withValues(alpha: 0.4)),
          const SizedBox(height: 12),
          Text(
            emptyHint ?? 'Trend data will appear here as you record more transactions.',
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 13, color: AppColors.textSecondary, height: 1.4),
          ),
        ],
      ),
    );
  }
}
