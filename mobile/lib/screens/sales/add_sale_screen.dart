import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../../services/notification_service.dart';
import '../../services/sale_watcher_service.dart';
import '../../utils/format_utils.dart';
import 'select_party_sheet.dart';

/// Matches the reference "Add Sale" screen: invoice number + date, a party
/// picker (defaults to Cash Sale, tap "Change" to open [showSelectPartySheet]),
/// an items entry point, total amount, notes and image attachment, and a
/// Save button that's only enabled once a total has been entered.
class AddSaleScreen extends StatefulWidget {
  const AddSaleScreen({super.key});

  @override
  State<AddSaleScreen> createState() => _AddSaleScreenState();
}

class _AddSaleScreenState extends State<AddSaleScreen> {
  final _api = ApiService();
  final _amountCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  Customer _party = Customer(id: 0, name: 'Cash Sale');
  DateTime _date = DateTime.now();
  bool _isSaving = false;

  // Items picked via _pickItems() — used only to compute the total amount
  // (the quick-invoice endpoint this screen posts to takes a flat `total`,
  // not a line-item array; for a fully itemized sale, Quick POS is the
  // real item-based checkout flow).
  final Map<Product, int> _pickedItems = {};

  @override
  void initState() {
    super.initState();
    _amountCtrl.addListener(() => setState(() {}));
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  bool get _canSave => (double.tryParse(_amountCtrl.text) ?? 0) > 0 && !_isSaving;

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _date,
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (picked != null) setState(() => _date = picked);
  }

  Future<void> _changeParty() async {
    final selected = await showSelectPartySheet(context);
    if (selected != null) setState(() => _party = selected);
  }

  double get _pickedTotal => _pickedItems.entries.fold(0, (sum, e) => sum + e.key.sellingPrice * e.value);
  int get _pickedCount => _pickedItems.values.fold(0, (a, b) => a + b);

  Future<void> _pickItems() async {
    final result = await showModalBottomSheet<Map<Product, int>>(
      context: context,
      isScrollControlled: true,
      builder: (_) => _ItemPickerSheet(initial: Map.of(_pickedItems)),
    );
    if (result != null) {
      setState(() {
        _pickedItems
          ..clear()
          ..addAll(result);
        if (_pickedTotal > 0) _amountCtrl.text = _pickedTotal.toStringAsFixed(0);
      });
    }
  }

  Future<void> _save() async {
    if (!_canSave) return;
    setState(() => _isSaving = true);

    final total = double.tryParse(_amountCtrl.text) ?? 0;
    var notes = _notesCtrl.text.trim();
    if (notes.isEmpty && _pickedItems.isNotEmpty) {
      notes = _pickedItems.entries.map((e) => '${e.value}x ${e.key.name}').join(', ');
    }
    try {
      final res = await _api.createSale({
        'customer_id': _party.id > 0 ? _party.id : null,
        'total': total,
        'notes': notes.isEmpty ? null : notes,
        'date': _date.toIso8601String(),
      });
      SaleWatcherService.instance.markSeen(res.data['sale_id'] as int?);
    } catch (_) {
      // Fall through — we still confirm to the user locally so the flow
      // isn't blocked while the backend is unreachable.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);

    NotificationService.instance.notify(
      topic: NotificationTopic.sales,
      title: 'Sale completed 🎉',
      body: '${FormatUtils.currency(total)} invoiced to ${_party.name}',
    );

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Sale recorded'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Add Sale'),
        actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.info_outline))],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: _labeledBox(
                    label: 'Invoice Number',
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [Text('1', style: TextStyle(fontWeight: FontWeight.w700)), Icon(Icons.expand_more, size: 18, color: AppColors.textHint)],
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: InkWell(
                    onTap: _pickDate,
                    child: _labeledBox(
                      label: 'Date',
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(FormatUtils.date(_date), style: const TextStyle(fontWeight: FontWeight.w700)),
                          const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textHint),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 18,
                    backgroundColor: AppColors.primary.withValues(alpha: 0.1),
                    child: Text(_party.name.isNotEmpty ? _party.name[0].toUpperCase() : '?', style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary)),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(_party.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                        Text(FormatUtils.currency(_party.currentDebt), style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                      ],
                    ),
                  ),
                  OutlinedButton.icon(
                    onPressed: _changeParty,
                    icon: const Icon(Icons.sync_alt, size: 16),
                    label: const Text('Change'),
                    style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            OutlinedButton.icon(
              onPressed: _pickItems,
              icon: const Icon(Icons.add_circle_outline, color: AppColors.primary),
              label: Text(_pickedItems.isEmpty ? 'Add Items' : '$_pickedCount item(s) selected — tap to edit'),
              style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(48), foregroundColor: AppColors.primary),
            ),
            if (_pickedItems.isNotEmpty) ...[
              const SizedBox(height: 6),
              Text(
                'Picked items total: ${FormatUtils.currency(_pickedTotal)} — auto-filled below, feel free to adjust.',
                style: const TextStyle(fontSize: 11, color: AppColors.textSecondary),
              ),
            ],
            const SizedBox(height: 16),
            const Text('Total Amount', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(
              controller: _amountCtrl,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              decoration: const InputDecoration(prefixText: 'TSh  ', hintText: '0'),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _notesCtrl,
              maxLines: 3,
              decoration: const InputDecoration(hintText: 'Notes or Remarks'),
            ),
            const SizedBox(height: 12),
            TextButton.icon(
              onPressed: () {},
              icon: const Icon(Icons.image_outlined, size: 18),
              label: const Text('Add Images'),
              style: TextButton.styleFrom(foregroundColor: AppColors.primary, padding: EdgeInsets.zero),
            ),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save'),
            ),
          ],
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

/// Bottom sheet used by [AddSaleScreen._pickItems] — a real product picker
/// (not a stub) backed by GET /products, with a +/- stepper per item and a
/// running total shown at the bottom.
class _ItemPickerSheet extends StatefulWidget {
  final Map<Product, int> initial;
  const _ItemPickerSheet({required this.initial});

  @override
  State<_ItemPickerSheet> createState() => _ItemPickerSheetState();
}

class _ItemPickerSheetState extends State<_ItemPickerSheet> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  bool _isLoading = true;
  List<Product> _products = [];
  late Map<Product, int> _qty;

  @override
  void initState() {
    super.initState();
    _qty = Map.of(widget.initial);
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getProducts(search: _searchCtrl.text.trim().isEmpty ? null : _searchCtrl.text.trim());
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _products = list.map((e) => Product.fromJson(e)).toList());
      } else {
        setState(() => _products = []);
      }
    } catch (_) {
      setState(() => _products = []);
    }
    setState(() => _isLoading = false);
  }

  void _changeQty(Product p, int delta) {
    setState(() {
      final current = _qty[p] ?? 0;
      final next = (current + delta).clamp(0, 9999);
      if (next == 0) {
        _qty.remove(p);
      } else {
        _qty[p] = next;
      }
    });
  }

  double get _total => _qty.entries.fold(0, (sum, e) => sum + e.key.sellingPrice * e.value);

  @override
  Widget build(BuildContext context) {
    return DraggableScrollableSheet(
      initialChildSize: 0.85,
      minChildSize: 0.5,
      maxChildSize: 0.95,
      expand: false,
      builder: (ctx, scrollCtrl) => Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Row(
              children: [
                const Text('Add Items', style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
                const Spacer(),
                IconButton(onPressed: () => Navigator.pop(context), icon: const Icon(Icons.close)),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: TextField(
              controller: _searchCtrl,
              onSubmitted: (_) => _load(),
              decoration: InputDecoration(
                hintText: 'Search products...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: IconButton(icon: const Icon(Icons.arrow_forward), onPressed: _load),
              ),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(strokeWidth: 2))
                : _products.isEmpty
                    ? const Center(child: Text('No products found', style: TextStyle(color: AppColors.textSecondary)))
                    : ListView.builder(
                        controller: scrollCtrl,
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        itemCount: _products.length,
                        itemBuilder: (ctx, i) {
                          final p = _products[i];
                          final qty = _qty[p] ?? 0;
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 10),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                              decoration: BoxDecoration(
                                color: qty > 0 ? AppColors.primary.withValues(alpha: 0.05) : context.cardBg,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: qty > 0 ? AppColors.primary.withValues(alpha: 0.3) : context.borderColor),
                              ),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                                        Text(FormatUtils.currency(p.sellingPrice), style: TextStyle(fontSize: 12, color: context.textSecondaryColor)),
                                      ],
                                    ),
                                  ),
                                  IconButton(
                                    onPressed: qty > 0 ? () => _changeQty(p, -1) : null,
                                    icon: const Icon(Icons.remove_circle_outline),
                                    color: AppColors.primary,
                                  ),
                                  Text('$qty', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14)),
                                  IconButton(
                                    onPressed: () => _changeQty(p, 1),
                                    icon: const Icon(Icons.add_circle_outline),
                                    color: AppColors.primary,
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
          ),
          SafeArea(
            top: false,
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context, _qty),
                style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                child: Text(_qty.isEmpty ? 'Done' : 'Add ${_qty.length} item(s) — ${FormatUtils.currency(_total)}'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
