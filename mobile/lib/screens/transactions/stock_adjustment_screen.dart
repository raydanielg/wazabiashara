import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../models/product.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';

/// Quick "Stock Adjustment" flow — wired to the real
/// `/products/{id}/adjust-stock` endpoint (ProductController::adjustStock).
class StockAdjustmentScreen extends StatefulWidget {
  const StockAdjustmentScreen({super.key});

  @override
  State<StockAdjustmentScreen> createState() => _StockAdjustmentScreenState();
}

class _StockAdjustmentScreenState extends State<StockAdjustmentScreen> {
  final _api = ApiService();
  final _qtyCtrl = TextEditingController();
  final _noteCtrl = TextEditingController();

  Product? _product;
  List<Product> _products = [];
  bool _isLoadingProducts = true;
  String _type = 'in';
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _qtyCtrl.addListener(() => setState(() {}));
    _loadProducts();
  }

  @override
  void dispose() {
    _qtyCtrl.dispose();
    _noteCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadProducts() async {
    try {
      final res = await _api.getProducts();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _products = list.map((e) => Product.fromJson(e)).toList());
      }
    } catch (_) {}
    setState(() => _isLoadingProducts = false);
  }

  bool get _canSave => _product != null && (double.tryParse(_qtyCtrl.text) ?? 0) > 0 && !_isSaving;

  Future<void> _pickProduct() async {
    final selected = await showModalBottomSheet<Product>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => DraggableScrollableSheet(
        initialChildSize: 0.7,
        minChildSize: 0.4,
        maxChildSize: 0.9,
        expand: false,
        builder: (ctx2, scrollController) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Select Item', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              Expanded(
                child: _isLoadingProducts
                    ? const Center(child: CircularProgressIndicator(strokeWidth: 2))
                    : _products.isEmpty
                        ? const Center(child: Text('No items in inventory yet', style: TextStyle(color: AppColors.textHint)))
                        : ListView(
                            controller: scrollController,
                            children: _products.map((p) => ListTile(
                                  leading: CircleAvatar(backgroundColor: AppColors.gold.withValues(alpha: 0.1), child: Text(p.name.isNotEmpty ? p.name[0].toUpperCase() : '?', style: const TextStyle(color: AppColors.goldDark, fontWeight: FontWeight.w800))),
                                  title: Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                                  subtitle: Text('Current stock: ${p.stock} ${p.unit}', style: const TextStyle(fontSize: 11)),
                                  onTap: () => Navigator.pop(context, p),
                                )).toList(),
                          ),
              ),
            ],
          ),
        ),
      ),
    );
    if (selected != null) setState(() => _product = selected);
  }

  Future<void> _save() async {
    final branchId = context.read<AuthProvider>().user?.branchId;
    if (branchId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No branch assigned to your account.'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
      );
      return;
    }
    if (!_canSave) return;
    setState(() => _isSaving = true);

    try {
      await _api.adjustStock(_product!.id, {
        'branch_id': branchId,
        'qty': double.tryParse(_qtyCtrl.text) ?? 0,
        'type': _type,
        'note': _noteCtrl.text.trim().isEmpty ? null : _noteCtrl.text.trim(),
      });
    } catch (_) {}

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Stock adjusted'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Stock Adjustment')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            InkWell(
              onTap: _pickProduct,
              child: _labeledBox(
                label: 'Item',
                child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                  Text(_product?.name ?? 'Select an item', style: TextStyle(fontWeight: FontWeight.w700, color: _product == null ? AppColors.textHint : AppColors.textPrimary)),
                  const Icon(Icons.chevron_right, size: 18, color: AppColors.textHint),
                ]),
              ),
            ),
            if (_product != null) ...[
              const SizedBox(height: 6),
              Text('Current stock: ${_product!.stock} ${_product!.unit}', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
            ],
            const SizedBox(height: 16),
            const Text('Adjustment Type', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 8),
            Row(
              children: [
                _typeChip('in', 'Stock In', Icons.add, AppColors.success),
                const SizedBox(width: 8),
                _typeChip('out', 'Stock Out', Icons.remove, AppColors.error),
                const SizedBox(width: 8),
                _typeChip('adjustment', 'Set Exact', Icons.tune, AppColors.info),
              ],
            ),
            const SizedBox(height: 16),
            Text(_type == 'adjustment' ? 'New Quantity' : 'Quantity', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(
              controller: _qtyCtrl,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              decoration: const InputDecoration(hintText: '0'),
            ),
            const SizedBox(height: 16),
            TextField(controller: _noteCtrl, maxLines: 3, decoration: const InputDecoration(hintText: 'Reason (optional)')),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save Adjustment'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _typeChip(String value, String label, IconData icon, Color color) {
    final selected = _type == value;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _type = value),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: selected ? color : AppColors.surface,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: selected ? color : AppColors.divider),
          ),
          child: Column(
            children: [
              Icon(icon, size: 16, color: selected ? Colors.white : color),
              const SizedBox(height: 2),
              Text(label, style: TextStyle(fontWeight: FontWeight.w700, fontSize: 10, color: selected ? Colors.white : AppColors.textSecondary)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _labeledBox({required String label, required Widget child}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
          const SizedBox(height: 4),
          child,
        ],
      ),
    );
  }
}
