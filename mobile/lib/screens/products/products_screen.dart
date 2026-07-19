import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/product.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final _searchCtrl = TextEditingController();
  bool _isLoading = false;
  String? _selectedCategory;

  final List<Product> _products = [
    Product(id: 1, name: 'Soda 500ml', sellingPrice: 3000, costPrice: 2000, stock: 120, unit: 'btl', category: 'Drinks', reorderLevel: 10),
    Product(id: 2, name: 'Rice 1kg', sellingPrice: 2500, costPrice: 1800, stock: 85, unit: 'kg', category: 'Food', reorderLevel: 10),
    Product(id: 3, name: 'Cooking Oil 1L', sellingPrice: 5000, costPrice: 3800, stock: 64, unit: 'ltr', category: 'Food', reorderLevel: 8),
    Product(id: 4, name: 'Sugar 1kg', sellingPrice: 3000, costPrice: 2200, stock: 52, unit: 'kg', category: 'Food', reorderLevel: 10),
    Product(id: 5, name: 'Bread', sellingPrice: 1500, costPrice: 900, stock: 3, unit: 'pcs', category: 'Bakery', reorderLevel: 10),
    Product(id: 6, name: 'Milk 1L', sellingPrice: 2000, costPrice: 1400, stock: 30, unit: 'ltr', category: 'Dairy', reorderLevel: 8),
    Product(id: 7, name: 'Eggs (tray)', sellingPrice: 9000, costPrice: 7000, stock: 18, unit: 'tray', category: 'Dairy', reorderLevel: 5),
    Product(id: 8, name: 'Tea 200g', sellingPrice: 1800, costPrice: 1100, stock: 40, unit: 'pcs', category: 'Drinks', reorderLevel: 10),
    Product(id: 9, name: 'Soap 1kg', sellingPrice: 3500, costPrice: 2500, stock: 2, unit: 'kg', category: 'Household', reorderLevel: 5),
    Product(id: 10, name: 'Salt 500g', sellingPrice: 800, costPrice: 500, stock: 60, unit: 'pcs', category: 'Food', reorderLevel: 10),
    Product(id: 11, name: 'Water 500ml', sellingPrice: 500, costPrice: 300, stock: 100, unit: 'btl', category: 'Drinks', reorderLevel: 20),
    Product(id: 12, name: 'Biscuits', sellingPrice: 1000, costPrice: 600, stock: 0, unit: 'pcs', category: 'Bakery', reorderLevel: 10),
  ];

  List<String> get _categories {
    final cats = _products.map((p) => p.category ?? 'Other').toSet().toList();
    cats.sort();
    return cats;
  }

  List<Product> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    return _products.where((p) {
      final matchesSearch = p.name.toLowerCase().contains(q);
      final matchesCat = _selectedCategory == null || p.category == _selectedCategory;
      return matchesSearch && matchesCat;
    }).toList();
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Products'),
        actions: [
          IconButton(onPressed: () => _showAddSheet(context), icon: const Icon(Icons.add_circle_outline)),
        ],
      ),
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
                    hintText: 'Search products...',
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
                      _categoryChip(null, 'All'),
                      ..._categories.map((c) => _categoryChip(c, c)),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _filtered.isEmpty
                ? const EmptyState(icon: Icons.inventory_2_outlined, title: 'No products found', subtitle: 'Try adjusting your search')
                : ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: _filtered.length,
                    itemBuilder: (ctx, i) => _ProductListTile(
                      product: _filtered[i],
                      onEdit: () => _showEditSheet(context, _filtered[i]),
                      onDelete: () => _confirmDelete(context, _filtered[i]),
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _categoryChip(String? value, String label) {
    final selected = _selectedCategory == value;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: GestureDetector(
        onTap: () => setState(() => _selectedCategory = value),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : AppColors.surface,
            border: Border.all(color: selected ? AppColors.primary : AppColors.divider),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Text(label, style: TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w700,
            color: selected ? Colors.white : AppColors.textSecondary,
          )),
        ),
      ),
    );
  }

  void _showAddSheet(BuildContext context) {
    _showProductSheet(context, product: null);
  }

  void _showEditSheet(BuildContext context, Product product) {
    _showProductSheet(context, product: product);
  }

  void _showProductSheet(BuildContext context, {Product? product}) {
    final isEdit = product != null;
    final nameCtrl = TextEditingController(text: product?.name ?? '');
    final priceCtrl = TextEditingController(text: product != null ? product.sellingPrice.toString() : '');
    final stockCtrl = TextEditingController(text: product != null ? product.stock.toString() : '');
    final costCtrl = TextEditingController(text: product != null ? product.costPrice.toString() : '');

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
        child: Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(isEdit ? 'Edit Product' : 'Add Product',
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 20),
              TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Product Name')),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(child: TextField(controller: priceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Selling Price'))),
                  const SizedBox(width: 12),
                  Expanded(child: TextField(controller: costCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Cost Price'))),
                ],
              ),
              const SizedBox(height: 12),
              TextField(controller: stockCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Stock Quantity')),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(isEdit ? 'Product updated' : 'Product added'),
                      backgroundColor: AppColors.success,
                      behavior: SnackBarBehavior.floating,
                    ),
                  );
                },
                child: Text(isEdit ? 'Save Changes' : 'Add Product'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _confirmDelete(BuildContext context, Product product) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Product'),
        content: Text('Are you sure you want to delete "${product.name}"?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              setState(() => _products.removeWhere((p) => p.id == product.id));
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('${product.name} deleted'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
              );
            },
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Delete'),
          ),
        ],
      ),
    );
  }
}

class _ProductListTile extends StatelessWidget {
  final Product product;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const _ProductListTile({required this.product, required this.onEdit, required this.onDelete});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.divider),
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        leading: Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(
            color: AppColors.primary.withValues(alpha: 0.08),
            borderRadius: BorderRadius.circular(12),
          ),
          child: const Icon(Icons.inventory_2_outlined, color: AppColors.primary),
        ),
        title: Text(product.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Row(
              children: [
                Text(FormatUtils.currencyShort(product.sellingPrice),
                    style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary, fontSize: 13)),
                const SizedBox(width: 12),
                if (product.isOutOfStock)
                  _badge('Out of Stock', AppColors.error)
                else if (product.isLowStock)
                  _badge('Low Stock', AppColors.warning)
                else
                  _badge('${product.stock} ${product.unit}', AppColors.success),
              ],
            ),
          ],
        ),
        trailing: PopupMenuButton(
          itemBuilder: (ctx) => [
            const PopupMenuItem(value: 'edit', child: Row(children: [
              Icon(Icons.edit_outlined, size: 20), SizedBox(width: 8), Text('Edit'),
            ])),
            const PopupMenuItem(value: 'delete', child: Row(children: [
              Icon(Icons.delete_outline, size: 20, color: AppColors.error), SizedBox(width: 8), Text('Delete'),
            ])),
          ],
          onSelected: (v) {
            if (v == 'edit') onEdit();
            if (v == 'delete') onDelete();
          },
        ),
      ),
    );
  }

  Widget _badge(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(text, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: color)),
    );
  }
}
