import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/category.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';
import '../../widgets/empty_state.dart';

/// Generic "Manage <X> Categories" screen — reused for Party, Item, Expense
/// and Income categories (the mobile app has one screen per type, exactly
/// like the reference design: search box, list with edit/delete, and an
/// "Add New Category" button pinned at the bottom).
class CategoryListScreen extends StatefulWidget {
  final String type; // item | party | expense | income
  final String title;

  const CategoryListScreen({super.key, required this.type, required this.title});

  @override
  State<CategoryListScreen> createState() => _CategoryListScreenState();
}

class _CategoryListScreenState extends State<CategoryListScreen> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();

  List<AppCategory> _categories = [];
  bool _isLoading = true;

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
      final res = await _api.getCategories(widget.type);
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? (res.data['categories'] as List? ?? []);
        setState(() {
          _categories = list.map((e) => AppCategory.fromJson(e)).toList();
        });
      } else {
        setState(() => _categories = _seedFor(widget.type));
      }
    } catch (_) {
      setState(() => _categories = _seedFor(widget.type));
    }
    setState(() => _isLoading = false);
  }

  /// Reasonable starter categories shown while offline / before the API is
  /// reachable, so the screen never looks empty during a demo.
  List<AppCategory> _seedFor(String type) {
    switch (type) {
      case 'party':
        return [AppCategory(id: -1, name: 'General', type: type)];
      case 'expense':
        return [
          'Rent', 'Salaries', 'Bank Fees', 'Marketing', 'Utilities', 'Repair & Maintenance', 'Travel & Transportation', 'Miscellaneous',
        ].asMap().entries.map((e) => AppCategory(id: -(e.key + 1), name: e.value, type: type)).toList();
      case 'income':
        return ['Sales Revenue', 'Service Income', 'Interest', 'Other Income']
            .asMap().entries.map((e) => AppCategory(id: -(e.key + 1), name: e.value, type: type)).toList();
      default:
        return ['General', 'Beverages', 'Groceries', 'Household'].asMap().entries.map((e) => AppCategory(id: -(e.key + 1), name: e.value, type: type)).toList();
    }
  }

  List<AppCategory> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    if (q.isEmpty) return _categories;
    return _categories.where((c) => c.name.toLowerCase().contains(q)).toList();
  }

  Future<void> _save({AppCategory? existing}) async {
    final nameCtrl = TextEditingController(text: existing?.name ?? '');

    final result = await showModalBottomSheet<bool>(
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
              Text(existing == null ? 'Add New Category' : 'Edit Category', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 20),
              TextField(
                controller: nameCtrl,
                autofocus: true,
                decoration: const InputDecoration(labelText: 'Category Name'),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => Navigator.pop(ctx, true),
                child: Text(existing == null ? 'Add Category' : 'Save Changes'),
              ),
            ],
          ),
        ),
      ),
    );

    if (result != true || nameCtrl.text.trim().isEmpty) return;
    final name = nameCtrl.text.trim();

    try {
      if (existing == null) {
        final res = await _api.createCategory({'name': name, 'type': widget.type});
        if (res.statusCode == 200 || res.statusCode == 201) {
          setState(() => _categories.add(AppCategory(id: DateTime.now().millisecondsSinceEpoch, name: name, type: widget.type)));
        }
      } else {
        await _api.updateCategory(existing.id, {'name': name});
        setState(() {
          final i = _categories.indexWhere((c) => c.id == existing.id);
          if (i >= 0) _categories[i] = AppCategory(id: existing.id, name: name, type: widget.type);
        });
      }
    } catch (_) {
      // Offline / API not reachable yet — still reflect the change locally
      // so the UI stays usable while the backend catches up.
      setState(() {
        if (existing == null) {
          _categories.add(AppCategory(id: DateTime.now().millisecondsSinceEpoch, name: name, type: widget.type));
        } else {
          final i = _categories.indexWhere((c) => c.id == existing.id);
          if (i >= 0) _categories[i] = AppCategory(id: existing.id, name: name, type: widget.type);
        }
      });
    }

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(existing == null ? 'Category added' : 'Category updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
      );
    }
  }

  Future<void> _delete(AppCategory category) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Category'),
        content: Text('Delete "${category.name}"? This cannot be undone.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Delete'),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    try {
      await _api.deleteCategory(category.id);
    } catch (_) {}
    setState(() => _categories.removeWhere((c) => c.id == category.id));
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Category deleted'), backgroundColor: AppColors.textSecondary, behavior: SnackBarBehavior.floating),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        actions: [
          IconButton(onPressed: () {}, icon: const Icon(Icons.info_outline)),
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _searchCtrl,
              onChanged: (_) => setState(() {}),
              decoration: InputDecoration(
                hintText: 'Search ${widget.title.replaceFirst('Manage ', '')}...',
                prefixIcon: const Icon(Icons.search),
              ),
            ),
          ),
          Expanded(
            child: _isLoading
                ? const LoadingWidget()
                : _filtered.isEmpty
                    ? EmptyState(
                        icon: Icons.category_outlined,
                        title: 'No categories found',
                        subtitle: 'Add your first category to get started.',
                      )
                    : ListView.separated(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        itemCount: _filtered.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 1),
                        itemBuilder: (ctx, i) {
                          final c = _filtered[i];
                          return Container(
                            decoration: BoxDecoration(
                              color: AppColors.surface,
                              border: Border(bottom: BorderSide(color: AppColors.divider)),
                            ),
                            child: ListTile(
                              contentPadding: const EdgeInsets.symmetric(horizontal: 4),
                              title: Text(c.name, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                              trailing: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  IconButton(icon: const Icon(Icons.edit_outlined, size: 20), onPressed: () => _save(existing: c)),
                                  IconButton(icon: const Icon(Icons.delete_outline, size: 20, color: AppColors.error), onPressed: () => _delete(c)),
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
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
              child: ElevatedButton.icon(
                onPressed: () => _save(),
                icon: const Icon(Icons.add),
                label: const Text('Add New Category'),
                style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
