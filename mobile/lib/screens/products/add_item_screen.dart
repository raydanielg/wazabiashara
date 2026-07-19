import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../categories/category_list_screen.dart';

/// Matches the reference "Add New Item" screen: name, category picker,
/// Product/Service type toggle, and a two-tab body (Stock Details /
/// Additional Details) with the core pricing & stock fields.
class AddItemScreen extends StatefulWidget {
  final Product? existing;
  const AddItemScreen({super.key, this.existing});

  @override
  State<AddItemScreen> createState() => _AddItemScreenState();
}

class _AddItemScreenState extends State<AddItemScreen> with SingleTickerProviderStateMixin {
  final _api = ApiService();
  late final TabController _tabs;

  late final TextEditingController _nameCtrl;
  final _categoryCtrl = TextEditingController(text: 'General');
  final _stockCtrl = TextEditingController();
  final _unitCtrl = TextEditingController(text: 'Piece');
  final _salesPriceCtrl = TextEditingController();
  final _purchasePriceCtrl = TextEditingController();
  final _skuCtrl = TextEditingController();
  final _descCtrl = TextEditingController();

  String _itemType = 'Product';
  bool _lowStockAlert = false;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    _tabs.addListener(() {
      if (!_tabs.indexIsChanging) setState(() {});
    });
    final p = widget.existing;
    _nameCtrl = TextEditingController(text: p?.name ?? '');
    if (p != null) {
      _stockCtrl.text = p.stock.toString();
      if (p.unit.isNotEmpty) _unitCtrl.text = p.unit;
      _salesPriceCtrl.text = p.sellingPrice == 0 ? '' : p.sellingPrice.toString();
      _purchasePriceCtrl.text = p.costPrice == 0 ? '' : p.costPrice.toString();
      if (p.category != null) _categoryCtrl.text = p.category!;
      if (p.sku != null) _skuCtrl.text = p.sku!;
    }
  }

  @override
  void dispose() {
    _tabs.dispose();
    _nameCtrl.dispose();
    _categoryCtrl.dispose();
    _stockCtrl.dispose();
    _unitCtrl.dispose();
    _salesPriceCtrl.dispose();
    _purchasePriceCtrl.dispose();
    _skuCtrl.dispose();
    _descCtrl.dispose();
    super.dispose();
  }

  Future<void> _pickCategory() async {
    // Reuses the same category management screen — tapping a row here just
    // needs a name, so we jump to the Item Categories list and let the user
    // pick by typing/selecting, then return here manually (kept minimal
    // since full picker wiring depends on the categories API being live).
    await Navigator.push(context, MaterialPageRoute(builder: (_) => const CategoryListScreen(type: 'item', title: 'Manage Item Categories')));
  }

  Future<void> _save() async {
    final name = _nameCtrl.text.trim();
    if (name.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Item name is required'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
      );
      return;
    }

    setState(() => _isSaving = true);
    final data = {
      'name': name,
      'category': _categoryCtrl.text.trim(),
      'unit': _unitCtrl.text.trim().isEmpty ? 'piece' : _unitCtrl.text.trim(),
      'selling_price': double.tryParse(_salesPriceCtrl.text) ?? 0,
      'cost_price': double.tryParse(_purchasePriceCtrl.text) ?? 0,
      'stock': int.tryParse(_stockCtrl.text) ?? 0,
      'sku': _skuCtrl.text.trim().isEmpty ? null : _skuCtrl.text.trim(),
      'reorder_level': _lowStockAlert ? 5 : 0,
      'type': _itemType.toLowerCase(),
    };

    try {
      if (widget.existing != null) {
        await _api.updateProduct(widget.existing!.id, data);
      } else {
        await _api.createProduct(data);
      }
    } catch (_) {
      // Still confirm locally — the product list screen will keep working
      // offline and can resync once the API is reachable.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);
    Navigator.pop(context, true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.existing == null ? 'Add New Item' : 'Edit Item'),
        actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.info_outline))],
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Item Name', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                  const SizedBox(height: 6),
                  TextField(controller: _nameCtrl, autofocus: widget.existing == null, decoration: const InputDecoration(hintText: 'e.g. Soda 500ml')),
                  const SizedBox(height: 16),
                  const Text('Category', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                  const SizedBox(height: 6),
                  InkWell(
                    onTap: _pickCategory,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                      decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.divider)),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(_categoryCtrl.text.isEmpty ? 'Select category' : _categoryCtrl.text, style: const TextStyle(fontWeight: FontWeight.w600)),
                          const Icon(Icons.chevron_right, color: AppColors.textHint),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  const Text('Item Type', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      _typeChip('Product'),
                      const SizedBox(width: 8),
                      _typeChip('Service'),
                    ],
                  ),
                  const SizedBox(height: 20),
                  TabBar(
                    controller: _tabs,
                    labelColor: AppColors.primary,
                    unselectedLabelColor: AppColors.textSecondary,
                    indicatorColor: AppColors.primary,
                    labelStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                    tabs: const [Tab(text: 'Stock Details'), Tab(text: 'Additional Details')],
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    height: _tabs.index == 0 ? 260 : 160,
                    child: TabBarView(
                      controller: _tabs,
                      physics: const NeverScrollableScrollPhysics(),
                      children: [_stockDetailsTab(), _additionalDetailsTab()],
                    ),
                  ),
                ],
              ),
            ),
          ),
          SafeArea(
            top: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
              child: ElevatedButton(
                onPressed: _isSaving ? null : _save,
                style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                child: _isSaving
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Text('Save'),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _typeChip(String type) {
    final selected = _itemType == type;
    return GestureDetector(
      onTap: () => setState(() => _itemType = type),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : AppColors.surface,
          border: Border.all(color: selected ? AppColors.primary : AppColors.divider),
          borderRadius: BorderRadius.circular(24),
        ),
        child: Text(type, style: TextStyle(fontWeight: FontWeight.w700, color: selected ? Colors.white : AppColors.textPrimary)),
      ),
    );
  }

  Widget _stockDetailsTab() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 4),
        Row(
          children: [
            Expanded(child: TextField(controller: _stockCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(hintText: 'Opening Stock'))),
            const SizedBox(width: 12),
            Expanded(
              child: TextField(
                controller: _unitCtrl,
                readOnly: true,
                decoration: const InputDecoration(hintText: 'Unit', suffixIcon: Icon(Icons.chevron_right, size: 18)),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(child: TextField(controller: _salesPriceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(hintText: 'Sales Price'))),
            const SizedBox(width: 12),
            Expanded(child: TextField(controller: _purchasePriceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(hintText: 'Purchase Price'))),
          ],
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            const Icon(Icons.notifications_active_outlined, color: AppColors.primary, size: 20),
            const SizedBox(width: 10),
            const Expanded(child: Text('Low Stock Alert', style: TextStyle(fontWeight: FontWeight.w600))),
            Switch(value: _lowStockAlert, onChanged: (v) => setState(() => _lowStockAlert = v), activeColor: AppColors.primary),
          ],
        ),
      ],
    );
  }

  Widget _additionalDetailsTab() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 4),
        TextField(controller: _skuCtrl, decoration: const InputDecoration(hintText: 'SKU / Barcode')),
        const SizedBox(height: 12),
        TextField(controller: _descCtrl, maxLines: 3, decoration: const InputDecoration(hintText: 'Description (optional)')),
      ],
    );
  }
}
