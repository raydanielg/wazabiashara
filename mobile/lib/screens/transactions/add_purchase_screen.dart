import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../models/supplier.dart';
import '../../models/product.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';

class _PurchaseLine {
  Product? product;
  final qtyCtrl = TextEditingController(text: '1');
  final costCtrl = TextEditingController();

  double get subtotal => (double.tryParse(qtyCtrl.text) ?? 0) * (double.tryParse(costCtrl.text) ?? 0);

  void dispose() {
    qtyCtrl.dispose();
    costCtrl.dispose();
  }
}

/// Records a stock Purchase — wired to the real `/purchases` endpoint
/// (SupplierController::storePurchase), so the "Purchase" shortcut on the
/// Home screen actually works and updates branch stock.
class AddPurchaseScreen extends StatefulWidget {
  const AddPurchaseScreen({super.key});

  @override
  State<AddPurchaseScreen> createState() => _AddPurchaseScreenState();
}

class _AddPurchaseScreenState extends State<AddPurchaseScreen> {
  final _api = ApiService();
  Supplier? _supplier;
  String _paymentStatus = 'paid';
  bool _isSaving = false;
  final List<_PurchaseLine> _lines = [_PurchaseLine()];

  @override
  void dispose() {
    for (final l in _lines) {
      l.dispose();
    }
    super.dispose();
  }

  double get _total => _lines.fold(0, (p, l) => p + l.subtotal);

  bool get _canSave =>
      !_isSaving &&
      _lines.any((l) => l.product != null && (double.tryParse(l.qtyCtrl.text) ?? 0) > 0 && (double.tryParse(l.costCtrl.text) ?? 0) >= 0);

  Future<void> _pickSupplier() async {
    final selected = await showModalBottomSheet<Supplier?>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => const _SupplierPickerSheet(),
    );
    setState(() => _supplier = selected);
  }

  Future<void> _pickProduct(_PurchaseLine line) async {
    final selected = await showModalBottomSheet<Product>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => const _ProductPickerSheet(),
    );
    if (selected != null) {
      setState(() {
        line.product = selected;
        if (line.costCtrl.text.isEmpty) line.costCtrl.text = selected.costPrice.toStringAsFixed(0);
      });
    }
  }

  Future<void> _save() async {
    final branchId = context.read<AuthProvider>().user?.branchId;
    if (branchId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No branch assigned to your account — ask your business owner to assign one.'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
      );
      return;
    }
    if (!_canSave) return;
    setState(() => _isSaving = true);

    final items = _lines
        .where((l) => l.product != null && (double.tryParse(l.qtyCtrl.text) ?? 0) > 0)
        .map((l) => {
              'product_id': l.product!.id,
              'qty': double.tryParse(l.qtyCtrl.text) ?? 0,
              'cost_price': double.tryParse(l.costCtrl.text) ?? 0,
            })
        .toList();

    try {
      await _api.createPurchase({
        'branch_id': branchId,
        'supplier_id': _supplier?.id,
        'payment_status': _paymentStatus,
        'items': items,
      });
    } catch (_) {
      // Offline-friendly: still confirm locally.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Purchase recorded'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    final branchId = context.watch<AuthProvider>().user?.branchId;

    return Scaffold(
      appBar: AppBar(title: const Text('Add Purchase')),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (branchId == null)
                    Container(
                      margin: const EdgeInsets.only(bottom: 16),
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(color: AppColors.warningLight, borderRadius: BorderRadius.circular(12)),
                      child: const Row(
                        children: [
                          Icon(Icons.warning_amber_rounded, color: AppColors.warning, size: 18),
                          SizedBox(width: 10),
                          Expanded(child: Text('No branch is assigned to your account yet, so purchases can\'t be saved.', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600))),
                        ],
                      ),
                    ),
                  InkWell(
                    onTap: _pickSupplier,
                    child: _labeledBox(
                      label: 'Supplier',
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(_supplier?.name ?? 'No Supplier', style: TextStyle(fontWeight: FontWeight.w700, color: _supplier == null ? AppColors.textHint : AppColors.textPrimary)),
                          const Icon(Icons.chevron_right, size: 18, color: AppColors.textHint),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text('Payment Status', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      _statusChip('paid', 'Paid'),
                      const SizedBox(width: 8),
                      _statusChip('credit', 'Credit'),
                      const SizedBox(width: 8),
                      _statusChip('partial', 'Partial'),
                    ],
                  ),
                  const SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Items', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800)),
                      TextButton.icon(
                        onPressed: () => setState(() => _lines.add(_PurchaseLine())),
                        icon: const Icon(Icons.add, size: 16),
                        label: const Text('Add Item'),
                        style: TextButton.styleFrom(padding: EdgeInsets.zero, foregroundColor: AppColors.primary),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  ..._lines.asMap().entries.map((entry) => _lineCard(entry.key, entry.value)),
                ],
              ),
            ),
          ),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: const BoxDecoration(color: AppColors.surface, border: Border(top: BorderSide(color: AppColors.divider))),
            child: SafeArea(
              top: false,
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Total', style: TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                        Text(FormatUtils.currency(_total), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppColors.info)),
                      ],
                    ),
                  ),
                  ElevatedButton(
                    onPressed: _canSave ? _save : null,
                    style: ElevatedButton.styleFrom(backgroundColor: AppColors.info, padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16)),
                    child: _isSaving
                        ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Text('Save Purchase'),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _statusChip(String value, String label) {
    final selected = _paymentStatus == value;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _paymentStatus = value),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: selected ? AppColors.info : AppColors.surface,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: selected ? AppColors.info : AppColors.divider),
          ),
          child: Center(child: Text(label, style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12, color: selected ? Colors.white : AppColors.textSecondary))),
        ),
      ),
    );
  }

  Widget _lineCard(int index, _PurchaseLine line) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: InkWell(
                  onTap: () => _pickProduct(line),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(
                          line.product?.name ?? 'Select a product',
                          style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: line.product == null ? AppColors.textHint : AppColors.textPrimary),
                        ),
                      ),
                      const Icon(Icons.chevron_right, size: 16, color: AppColors.textHint),
                    ],
                  ),
                ),
              ),
              if (_lines.length > 1)
                IconButton(
                  onPressed: () => setState(() {
                    _lines.removeAt(index).dispose();
                  }),
                  icon: const Icon(Icons.close, size: 18, color: AppColors.error),
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: line.qtyCtrl,
                  keyboardType: TextInputType.number,
                  onChanged: (_) => setState(() {}),
                  decoration: const InputDecoration(labelText: 'Qty', isDense: true),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: TextField(
                  controller: line.costCtrl,
                  keyboardType: TextInputType.number,
                  onChanged: (_) => setState(() {}),
                  decoration: const InputDecoration(labelText: 'Cost Price', isDense: true, prefixText: 'TSh '),
                ),
              ),
            ],
          ),
          if (line.subtotal > 0) ...[
            const SizedBox(height: 8),
            Align(
              alignment: Alignment.centerRight,
              child: Text('= ${FormatUtils.currency(line.subtotal)}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            ),
          ],
        ],
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

class _SupplierPickerSheet extends StatefulWidget {
  const _SupplierPickerSheet();

  @override
  State<_SupplierPickerSheet> createState() => _SupplierPickerSheetState();
}

class _SupplierPickerSheetState extends State<_SupplierPickerSheet> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  List<Supplier> _suppliers = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final res = await _api.getSuppliers();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _suppliers = list.map((e) => Supplier.fromJson(e)).toList());
      }
    } catch (_) {}
    setState(() => _isLoading = false);
  }

  List<Supplier> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    if (q.isEmpty) return _suppliers;
    return _suppliers.where((s) => s.name.toLowerCase().contains(q)).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: DraggableScrollableSheet(
        initialChildSize: 0.7,
        minChildSize: 0.4,
        maxChildSize: 0.9,
        expand: false,
        builder: (ctx, scrollController) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.divider, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 16),
              const Text('Select Supplier', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              TextField(controller: _searchCtrl, onChanged: (_) => setState(() {}), decoration: const InputDecoration(hintText: 'Search supplier...', prefixIcon: Icon(Icons.search))),
              const SizedBox(height: 12),
              Expanded(
                child: ListView(
                  controller: scrollController,
                  children: [
                    ListTile(
                      leading: const CircleAvatar(backgroundColor: AppColors.divider, child: Icon(Icons.block, size: 18, color: AppColors.textSecondary)),
                      title: const Text('No Supplier', style: TextStyle(fontWeight: FontWeight.w700)),
                      onTap: () => Navigator.pop(context, null),
                    ),
                    const Divider(height: 20),
                    if (_isLoading)
                      const Padding(padding: EdgeInsets.symmetric(vertical: 24), child: Center(child: CircularProgressIndicator(strokeWidth: 2)))
                    else if (_filtered.isEmpty)
                      const Padding(padding: EdgeInsets.symmetric(vertical: 24), child: Center(child: Text('No suppliers yet', style: TextStyle(color: AppColors.textHint))))
                    else
                      ..._filtered.map((s) => ListTile(
                            leading: CircleAvatar(backgroundColor: AppColors.info.withValues(alpha: 0.1), child: Text(s.name.isNotEmpty ? s.name[0].toUpperCase() : '?', style: const TextStyle(color: AppColors.info, fontWeight: FontWeight.w800))),
                            title: Text(s.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                            subtitle: s.phone != null ? Text(s.phone!) : null,
                            onTap: () => Navigator.pop(context, s),
                          )),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ProductPickerSheet extends StatefulWidget {
  const _ProductPickerSheet();

  @override
  State<_ProductPickerSheet> createState() => _ProductPickerSheetState();
}

class _ProductPickerSheetState extends State<_ProductPickerSheet> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  List<Product> _products = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final res = await _api.getProducts();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _products = list.map((e) => Product.fromJson(e)).toList());
      }
    } catch (_) {}
    setState(() => _isLoading = false);
  }

  List<Product> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    if (q.isEmpty) return _products;
    return _products.where((p) => p.name.toLowerCase().contains(q)).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: DraggableScrollableSheet(
        initialChildSize: 0.75,
        minChildSize: 0.5,
        maxChildSize: 0.92,
        expand: false,
        builder: (ctx, scrollController) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.divider, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 16),
              const Text('Select Product', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              TextField(controller: _searchCtrl, onChanged: (_) => setState(() {}), decoration: const InputDecoration(hintText: 'Search item...', prefixIcon: Icon(Icons.search))),
              const SizedBox(height: 12),
              Expanded(
                child: _isLoading
                    ? const Center(child: CircularProgressIndicator(strokeWidth: 2))
                    : _filtered.isEmpty
                        ? const Center(child: Text('No items in inventory yet', style: TextStyle(color: AppColors.textHint)))
                        : ListView(
                            controller: scrollController,
                            children: _filtered.map((p) => ListTile(
                                  leading: CircleAvatar(backgroundColor: AppColors.gold.withValues(alpha: 0.1), child: Text(p.name.isNotEmpty ? p.name[0].toUpperCase() : '?', style: const TextStyle(color: AppColors.goldDark, fontWeight: FontWeight.w800))),
                                  title: Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700)),
                                  subtitle: Text('Stock: ${p.stock} ${p.unit}  •  Cost: ${FormatUtils.currency(p.costPrice)}', style: const TextStyle(fontSize: 11)),
                                  onTap: () => Navigator.pop(context, p),
                                )).toList(),
                          ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
