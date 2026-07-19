import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/sale.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_widget.dart';

class SalesScreen extends StatefulWidget {
  const SalesScreen({super.key});

  @override
  State<SalesScreen> createState() => _SalesScreenState();
}

class _SalesScreenState extends State<SalesScreen> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  String _filter = 'all';
  bool _isLoading = true;
  List<Sale> _sales = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
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

  List<Sale> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    return _sales.where((s) {
      final matchesSearch = s.receiptNo.toLowerCase().contains(q) || (s.customerName?.toLowerCase().contains(q) ?? false);
      final matchesFilter = _filter == 'all' || s.paymentMethod == _filter;
      return matchesSearch && matchesFilter;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Sales')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                TextField(
                  controller: _searchCtrl,
                  onChanged: (_) => setState(() {}),
                  decoration: InputDecoration(
                    hintText: 'Search by receipt or customer...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchCtrl.text.isNotEmpty
                        ? IconButton(onPressed: () { _searchCtrl.clear(); setState(() {}); }, icon: const Icon(Icons.clear))
                        : null,
                  ),
                ),
                const SizedBox(height: 12),
                SizedBox(
                  height: 36,
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    children: [
                      _filterChip(context, 'all', 'All'),
                      _filterChip(context, 'cash', 'Cash'),
                      _filterChip(context, 'mpesa', 'M-Pesa'),
                      _filterChip(context, 'bank', 'Bank'),
                      _filterChip(context, 'credit', 'Credit'),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _isLoading
                ? const LoadingWidget(message: 'Loading sales...')
                : RefreshIndicator(
                    onRefresh: _load,
                    child: _filtered.isEmpty
                        ? ListView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            children: const [
                              SizedBox(height: 80),
                              EmptyState(icon: Icons.receipt_long_outlined, title: 'No sales found'),
                            ],
                          )
                        : ListView.builder(
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            itemCount: _filtered.length,
                            itemBuilder: (ctx, i) => _SaleListTile(
                              sale: _filtered[i],
                              onTap: () => _showDetail(context, _filtered[i]),
                            ),
                          ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _filterChip(BuildContext context, String value, String label) {
    final selected = _filter == value;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: GestureDetector(
        onTap: () => setState(() => _filter = value),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : context.cardBg,
            border: Border.all(color: selected ? AppColors.primary : context.borderColor),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Text(label, style: TextStyle(
            fontSize: 13, fontWeight: FontWeight.w700,
            color: selected ? Colors.white : AppColors.textSecondary,
          )),
        ),
      ),
    );
  }

  void _showDetail(BuildContext context, Sale sale) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(sale.receiptNo, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.success.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(sale.status.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: AppColors.success)),
                ),
              ],
            ),
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
            _detailRow('Subtotal', FormatUtils.currency(sale.subtotal)),
            if (sale.discount > 0) _detailRow('Discount', '-${FormatUtils.currency(sale.discount)}', color: AppColors.error),
            _detailRow('Total', FormatUtils.currency(sale.total), bold: true),
            _detailRow('Paid', FormatUtils.currency(sale.paid)),
            if (sale.change > 0) _detailRow('Change', FormatUtils.currency(sale.change)),
            const Divider(height: 32),
            _detailRow('Payment Method', sale.paymentMethod.toUpperCase()),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => Navigator.pop(ctx),
                    icon: const Icon(Icons.close),
                    label: const Text('Close'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.pop(ctx);
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Receipt printing...'), behavior: SnackBarBehavior.floating),
                      );
                    },
                    icon: const Icon(Icons.print_outlined),
                    label: const Text('Print'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _detailRow(String label, String value, {bool bold = false, Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(fontSize: 14, color: AppColors.textSecondary, fontWeight: bold ? FontWeight.w700 : FontWeight.w400)),
          Text(value, style: TextStyle(fontSize: 14, fontWeight: bold ? FontWeight.w800 : FontWeight.w600, color: color ?? AppColors.textPrimary)),
        ],
      ),
    );
  }
}

class _SaleListTile extends StatelessWidget {
  final Sale sale;
  final VoidCallback onTap;

  const _SaleListTile({required this.sale, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final isCash = sale.paymentMethod == 'cash';
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: context.cardBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: context.borderColor),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: (isCash ? AppColors.success : AppColors.info).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(isCash ? Icons.payments : Icons.phone_iphone, color: isCash ? AppColors.success : AppColors.info, size: 22),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(sale.receiptNo, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  Text(
                    sale.date != null ? FormatUtils.relativeTime(sale.date!) : '',
                    style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(FormatUtils.currency(sale.total), style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.success, fontSize: 14)),
                Text(sale.paymentMethod, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
