import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../utils/format_utils.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';

class _SaleRow {
  final String receiptNo;
  final double total;
  final String paymentMethod;
  final DateTime? date;
  final String? customerName;

  _SaleRow({required this.receiptNo, required this.total, required this.paymentMethod, this.date, this.customerName});

  factory _SaleRow.fromJson(Map<String, dynamic> j) => _SaleRow(
        receiptNo: j['receipt_no'] as String? ?? '',
        total: (j['total'] as num?)?.toDouble() ?? 0,
        paymentMethod: j['payment_method'] as String? ?? 'cash',
        date: j['created_at'] != null ? DateTime.tryParse(j['created_at']) : null,
        customerName: (j['customer'] as Map<String, dynamic>?)?['name'] as String?,
      );
}

/// Matches the reference "Sales Report" screen: a date-range header with a
/// Change action, a filter icon, and a transaction list pulled from the real
/// /reports/sales endpoint (ReportController::sales) — with a genuine
/// empty state when nothing matches the selected range.
class SalesReportScreen extends StatefulWidget {
  const SalesReportScreen({super.key});

  @override
  State<SalesReportScreen> createState() => _SalesReportScreenState();
}

class _SalesReportScreenState extends State<SalesReportScreen> {
  final _api = ApiService();
  DateTimeRange _range = DateTimeRange(
    start: DateTime(DateTime.now().year, DateTime.now().month, 1),
    end: DateTime(DateTime.now().year, DateTime.now().month + 1, 0),
  );

  bool _isLoading = true;
  List<_SaleRow> _sales = [];

  String get _rangeLabel {
    final now = DateTime.now();
    final isThisMonth = _range.start.year == now.year && _range.start.month == now.month && _range.start.day == 1;
    final prefix = isThisMonth ? 'This Month' : FormatUtils.date(_range.start);
    return '$prefix   ${FormatUtils.date(_range.start)} - ${FormatUtils.date(_range.end)}';
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getSalesReport(
        from: _range.start.toIso8601String().split('T').first,
        to: _range.end.toIso8601String().split('T').first,
      );
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _sales = list.map((e) => _SaleRow.fromJson(e)).toList());
      } else {
        setState(() => _sales = []);
      }
    } catch (_) {
      setState(() => _sales = []);
    }
    setState(() => _isLoading = false);
  }

  Future<void> _changeRange() async {
    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
      initialDateRange: _range,
    );
    if (picked != null) {
      setState(() => _range = picked);
      _load();
    }
  }

  @override
  Widget build(BuildContext context) {
    final total = _sales.fold<double>(0, (p, s) => p + s.total);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Sales Report'),
        actions: [IconButton(onPressed: _load, icon: const Icon(Icons.refresh))],
      ),
      body: Column(
        children: [
          InkWell(
            onTap: _changeRange,
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppColors.divider))),
              child: Row(
                children: [
                  const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textSecondary),
                  const SizedBox(width: 8),
                  Expanded(child: Text(_rangeLabel, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textSecondary))),
                  const Text('CHANGE', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppColors.primary)),
                ],
              ),
            ),
          ),
          if (!_isLoading && _sales.isNotEmpty)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              color: AppColors.primary.withValues(alpha: 0.05),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('${_sales.length} transactions', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
                  Text(FormatUtils.currency(total), style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppColors.primary)),
                ],
              ),
            ),
          Expanded(
            child: _isLoading
                ? const LoadingWidget()
                : _sales.isEmpty
                    ? const Center(
                        child: Padding(
                          padding: EdgeInsets.all(32),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.description_outlined, size: 64, color: AppColors.textHint),
                              SizedBox(height: 16),
                              Text('No Transactions Found', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                            ],
                          ),
                        ),
                      )
                    : ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: _sales.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 8),
                        itemBuilder: (ctx, i) {
                          final s = _sales[i];
                          final isCash = s.paymentMethod.toLowerCase() == 'cash';
                          return Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            decoration: BoxDecoration(
                              color: AppColors.surface,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: AppColors.divider),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  width: 40, height: 40,
                                  decoration: BoxDecoration(color: (isCash ? AppColors.success : AppColors.info).withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
                                  child: Icon(isCash ? Icons.payments : Icons.phone_iphone, color: isCash ? AppColors.success : AppColors.info, size: 20),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(s.receiptNo, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                                      Text(
                                        '${s.customerName ?? 'Cash Sale'} • ${s.date != null ? FormatUtils.date(s.date!) : ''}',
                                        style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                                Text(FormatUtils.currency(s.total), style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.success)),
                              ],
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
