import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import '../../widgets/loading_widget.dart';

/// "Backup Information" — Wazabiashara is server-backed, not a local-only
/// app: every sale, item and payment is written straight to your account on
/// our servers the moment you save it, so there's no separate "backup" step
/// to run. This screen makes that honest and visible instead of promising a
/// manual backup feature that doesn't exist, and shows real counts of what's
/// currently safely stored for this business.
class BackupInfoScreen extends StatefulWidget {
  const BackupInfoScreen({super.key});

  @override
  State<BackupInfoScreen> createState() => _BackupInfoScreenState();
}

class _BackupInfoScreenState extends State<BackupInfoScreen> {
  final _api = ApiService();
  bool _isLoading = true;
  Map<String, dynamic>? _data;
  DateTime? _checkedAt;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getDashboard();
      if (res.statusCode == 200 && res.data['success'] == true) {
        setState(() {
          _data = res.data['data'] as Map<String, dynamic>;
          _checkedAt = DateTime.now();
        });
      }
    } catch (_) {}
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Backup Information')),
      body: _isLoading
          ? const LoadingWidget(message: 'Checking your data...')
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: AppColors.success.withValues(alpha: 0.08),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: AppColors.success.withValues(alpha: 0.2)),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Icon(Icons.cloud_done_outlined, color: AppColors.success, size: 22),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            'Your data is always backed up automatically. Every sale, item and payment is saved straight to your Wazabiashara account on our servers — there\'s nothing you need to do.',
                            style: TextStyle(fontSize: 13, height: 1.5, color: context.textSecondaryColor, fontWeight: FontWeight.w600),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  if (_data != null) ...[
                    Text('What\'s currently stored', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: context.textSecondaryColor)),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        Expanded(child: _statCard(context, Icons.inventory_2_outlined, 'Products', '${_data!['total_products'] ?? 0}')),
                        const SizedBox(width: 12),
                        Expanded(child: _statCard(context, Icons.people_outline, 'Customers', '${_data!['total_customers'] ?? 0}')),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(child: _statCard(context, Icons.point_of_sale_outlined, 'Sales this month', FormatUtils.currencyShort((_data!['month_sales'] as num?)?.toDouble() ?? 0))),
                        const SizedBox(width: 12),
                        Expanded(child: _statCard(context, Icons.receipt_long_outlined, 'Recent bills', '${(_data!['recent_sales'] as List?)?.length ?? 0}')),
                      ],
                    ),
                    const SizedBox(height: 20),
                    if (_checkedAt != null)
                      Center(
                        child: Text(
                          'Last checked ${FormatUtils.relativeTime(_checkedAt!)}',
                          style: TextStyle(fontSize: 11, color: context.textSecondaryColor),
                        ),
                      ),
                  ],
                ],
              ),
            ),
    );
  }

  Widget _statCard(BuildContext context, IconData icon, String label, String value) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(14), border: Border.all(color: context.borderColor)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: AppColors.primary, size: 20),
          const SizedBox(height: 10),
          Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
          const SizedBox(height: 2),
          Text(label, style: TextStyle(fontSize: 11, color: context.textSecondaryColor)),
        ],
      ),
    );
  }
}
