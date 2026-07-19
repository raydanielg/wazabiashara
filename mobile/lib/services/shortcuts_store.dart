import 'package:flutter/material.dart';

class ShortcutItem {
  final String label;
  final IconData icon;
  bool visible;

  ShortcutItem({required this.label, required this.icon, this.visible = true});
}

/// In-memory store for the Home screen's "Shortcuts" grid — which items are
/// shown and in what order. Backed by a ChangeNotifier so both the Home tab
/// and the Edit Shortcuts screen stay in sync without extra plumbing.
///
/// This is intentionally local-only (no persistence) for now, matching the
/// rest of the app's offline-friendly, best-effort approach where a backend
/// endpoint for it doesn't exist yet.
class ShortcutsStore extends ChangeNotifier {
  ShortcutsStore._internal();
  static final ShortcutsStore instance = ShortcutsStore._internal();

  final List<ShortcutItem> _items = [
    ShortcutItem(label: 'Add Party', icon: Icons.person_add_alt_outlined),
    ShortcutItem(label: 'Sales Invoice', icon: Icons.receipt_long_outlined),
    ShortcutItem(label: 'Payment In', icon: Icons.arrow_downward),
    ShortcutItem(label: 'Payment Out', icon: Icons.arrow_upward),
    ShortcutItem(label: 'Purchase', icon: Icons.shopping_cart_outlined),
    ShortcutItem(label: 'Add Item', icon: Icons.add_box_outlined),
    ShortcutItem(label: 'Expense', icon: Icons.money_off_outlined),
    ShortcutItem(label: 'Add Note', icon: Icons.note_add_outlined),
    ShortcutItem(label: 'Other Income', icon: Icons.savings_outlined, visible: false),
    ShortcutItem(label: 'Sales Return', icon: Icons.assignment_return_outlined, visible: false),
    ShortcutItem(label: 'Purchase Return', icon: Icons.undo_outlined, visible: false),
    ShortcutItem(label: 'Quotation', icon: Icons.description_outlined, visible: false),
    ShortcutItem(label: 'Stock Adjustment', icon: Icons.inventory_outlined, visible: false),
    ShortcutItem(label: 'Add Reminder', icon: Icons.alarm_add_outlined, visible: false),
  ];

  List<ShortcutItem> get all => List.unmodifiable(_items);
  List<ShortcutItem> get visible => _items.where((i) => i.visible).toList();

  void toggle(ShortcutItem item, bool value) {
    item.visible = value;
    notifyListeners();
  }

  void reorder(int oldIndex, int newIndex) {
    if (newIndex > oldIndex) newIndex -= 1;
    final item = _items.removeAt(oldIndex);
    _items.insert(newIndex, item);
    notifyListeners();
  }
}
