import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/sale.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_widget.dart';

/// Bill Gallery — a visual grid of past sale receipts, pulled from the same
/// real /sales endpoint the Transactions tab uses. Replaces the "coming
/// soon" placeholder that used to sit on the More menu.
class BillGalleryScreen extends StatefulWidget {
  const BillGalleryScreen({super.key});

  @override
  State<BillGalleryScreen> createState() => _BillGalleryScreenState();
}

class _BillGalleryScreenState extends State<BillGalleryScreen> {
  final _api = ApiService();
  bool _isLoading = true;
  List<Sale> _sales = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getSales();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _sales = list.map((e) => Sale.fromJson(e)).toList());
      } else {
        setState(() => _sales = []);
      }
    } catch (_) {
      setState(() => _sales = []);
    }
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Bill Gallery')),
      body: _isLoading
          ? const LoadingWidget(message: 'Loading your bills...')
          : RefreshIndicator(
              onRefresh: _load,
              child: _sales.isEmpty
                  ? ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: const [
                        SizedBox(height: 100),
                        EmptyState(icon: Icons.photo_library_outlined, title: 'No bills yet', subtitle: 'Every sale you record will show up here as a bill card.'),
                      ],
                    )
                  : GridView.builder(
                      padding: const EdgeInsets.all(16),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        mainAxisSpacing: 14,
                        crossAxisSpacing: 14,
                        childAspectRatio: 0.82,
                      ),
                      itemCount: _sales.length,
                      itemBuilder: (ctx, i) => _BillCard(sale: _sales[i]),
                    ),
            ),
    );
  }
}

class _BillCard extends StatelessWidget {
  final Sale sale;
  const _BillCard({required this.sale});

  @override
  Widget build(BuildContext context) {
    final isCash = sale.paymentMethod == 'cash';
    return InkWell(
      onTap: () => _showBill(context, sale),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: context.cardBg,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: context.borderColor),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: (isCash ? AppColors.success : AppColors.info).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(Icons.receipt_long, color: isCash ? AppColors.success : AppColors.info, size: 18),
            ),
            const Spacer(),
            Text(sale.receiptNo, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13), overflow: TextOverflow.ellipsis),
            const SizedBox(height: 4),
            Text(
              sale.date != null ? FormatUtils.relativeTime(sale.date!) : '',
              style: TextStyle(fontSize: 11, color: context.textSecondaryColor),
            ),
            const SizedBox(height: 8),
            Text(FormatUtils.currency(sale.total), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppColors.primary)),
          ],
        ),
      ),
    );
  }

  void _showBill(BuildContext context, Sale sale) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(sale.receiptNo, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            Text(sale.date != null ? FormatUtils.dateTime(sale.date!) : '', style: const TextStyle(color: AppColors.textSecondary, fontSize: 13)),
            if (sale.customerName != null) ...[
              const SizedBox(height: 4),
              Text('Customer: ${sale.customerName}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
            ],
            if (sale.items != null && sale.items!.isNotEmpty) ...[
              const Divider(height: 32),
              ...sale.items!.map((it) => Padding(
                    padding: const EdgeInsets.symmetric(vertical: 3),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(child: Text('${it.qty} x ${it.name}', style: const TextStyle(fontSize: 13), overflow: TextOverflow.ellipsis)),
                        Text(FormatUtils.currency(it.subtotal), style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                      ],
                    ),
                  )),
            ],
            const Divider(height: 32),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              const Text('Total', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
              Text(FormatUtils.currency(sale.total), style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            ]),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(onPressed: () => Navigator.pop(ctx), icon: const Icon(Icons.close), label: const Text('Close')),
            ),
          ],
        ),
      ),
    );
  }
}
