import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/shortcuts_store.dart';

/// Matches the reference "Edit Shortcuts" sheet: a reorderable, checkable
/// list controlling which quick-action tiles show up in the Home screen's
/// "Shortcuts" grid.
class EditShortcutsScreen extends StatefulWidget {
  const EditShortcutsScreen({super.key});

  @override
  State<EditShortcutsScreen> createState() => _EditShortcutsScreenState();
}

class _EditShortcutsScreenState extends State<EditShortcutsScreen> {
  final _store = ShortcutsStore.instance;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Edit Shortcuts')),
      body: ReorderableListView(
        padding: const EdgeInsets.symmetric(vertical: 8),
        onReorder: (oldIndex, newIndex) => setState(() => _store.reorder(oldIndex, newIndex)),
        header: Container(
          key: const ValueKey('_header'),
          margin: const EdgeInsets.fromLTRB(16, 8, 16, 12),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.06), borderRadius: BorderRadius.circular(12)),
          child: const Row(
            children: [
              Icon(Icons.info_outline, size: 16, color: AppColors.primary),
              SizedBox(width: 10),
              Expanded(
                child: Text(
                  'Tick to add a shortcut to your Home screen, untick to remove it. Drag to reorder.',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.primary),
                ),
              ),
            ],
          ),
        ),
        children: [
          for (final item in _store.all)
            Container(
              key: ValueKey(item.label),
              decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppColors.divider))),
              child: ListTile(
                leading: const Icon(Icons.drag_indicator, color: AppColors.textHint),
                title: Row(
                  children: [
                    Container(
                      width: 32,
                      height: 32,
                      decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
                      child: Icon(item.icon, size: 16, color: AppColors.primary),
                    ),
                    const SizedBox(width: 12),
                    Text(item.label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                  ],
                ),
                trailing: Checkbox(
                  value: item.visible,
                  activeColor: AppColors.primary,
                  onChanged: (v) => setState(() => _store.toggle(item, v ?? true)),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
