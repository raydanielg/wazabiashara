import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/product.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_widget.dart';
import '../../services/api_service.dart';
import 'add_item_screen.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  bool _isLoading = true;
  String? _selectedCategory;

  List<Product> _products = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getProducts();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _products = list.map((e) => Product.fromJson(e)).toList());
      }
    } catch (_) {
      // No connectivity yet — show the genuine empty state.
    }
    setState(() => _isLoading = false);
  }

  Future<void> _openAddItem({Product? existing}) async {
    final saved = await Navigator.push<bool>(context, MaterialPageRoute(builder: (_) => AddItemScreen(existing: existing)));
    if (saved == true) _load();
  }

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
        title: const Text('Inventory'),
        actions: [
          IconButton(onPressed: () => _openAddItem(), icon: const Icon(Icons.add_circle_outline)),
        ],
      ),
      body: _isLoading
          ? const LoadingWidget()
          : _products.isEmpty
              ? EmptyState(
                  icon: Icons.inventory_2_outlined,
                  title: 'No Items in Inventory',
                  subtitle: 'Your inventory is currently empty. Start by creating a item to manage it.',
                  actionLabel: 'Add New Item',
                  onAction: () => _openAddItem(),
                )
              : Column(
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
                          ? const EmptyState(icon: Icons.search_off, title: 'No products found', subtitle: 'Try adjusting your search')
                          : ListView.builder(
                              padding: const EdgeInsets.symmetric(horizontal: 16),
                              itemCount: _filtered.length,
                              itemBuilder: (ctx, i) => _ProductListTile(
                                product: _filtered[i],
                                onEdit: () => _openAddItem(existing: _filtered[i]),
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

  void _confirmDelete(BuildContext context, Product product) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Product'),
        content: Text('Are you sure you want to delete "${product.name}"?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              try {
                await _api.deleteProduct(product.id);
              } catch (_) {}
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
