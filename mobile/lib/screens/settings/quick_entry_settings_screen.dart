import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Quick Entry Settings" screen: three top-level
/// toggles plus a reorderable "Transaction Display" checklist controlling
/// which transaction types appear as tabs on the Quick Entry screen.
class QuickEntrySettingsScreen extends StatefulWidget {
  const QuickEntrySettingsScreen({super.key});

  @override
  State<QuickEntrySettingsScreen> createState() => _QuickEntrySettingsScreenState();
}

class _QuickEntrySettingsScreenState extends State<QuickEntrySettingsScreen> {
  bool _quickStart = false;
  bool _notesEnabled = false;
  bool _dateEnabled = false;

  final List<_DisplayItem> _display = [
    _DisplayItem('Sale', true),
    _DisplayItem('Purchase', true),
    _DisplayItem('Payment In', true),
    _DisplayItem('Payment Out', true),
    _DisplayItem('Expense', true),
    _DisplayItem('Other Income', true),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Quick Entry Settings')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.divider)),
            child: Column(
              children: [
                _toggle('Quick Start', 'Show Quick Entry at first app open to record data faster', _quickStart, (v) => setState(() => _quickStart = v)),
                const Divider(height: 1),
                _toggle('Notes or Remarks', 'You can add notes if you enable notes or remarks', _notesEnabled, (v) => setState(() => _notesEnabled = v)),
                const Divider(height: 1),
                _toggle('Enable Date', 'Record Transaction date also', _dateEnabled, (v) => setState(() => _dateEnabled = v)),
              ],
            ),
          ),
          const SizedBox(height: 20),
          const Padding(
            padding: EdgeInsets.only(left: 4, bottom: 8),
            child: Text('Transaction Display', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
          ),
          Container(
            decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.divider)),
            child: ReorderableListView(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              onReorder: (oldIndex, newIndex) {
                setState(() {
                  if (newIndex > oldIndex) newIndex -= 1;
                  final item = _display.removeAt(oldIndex);
                  _display.insert(newIndex, item);
                });
              },
              children: [
                for (int i = 0; i < _display.length; i++)
                  ListTile(
                    key: ValueKey(_display[i].label),
                    leading: const Icon(Icons.drag_indicator, color: AppColors.textHint),
                    title: Text(_display[i].label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    trailing: Checkbox(
                      value: _display[i].visible,
                      activeColor: AppColors.primary,
                      onChanged: (v) => setState(() => _display[i].visible = v ?? true),
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _toggle(String title, String subtitle, bool value, ValueChanged<bool> onChanged) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                const SizedBox(height: 2),
                Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }
}

class _DisplayItem {
  final String label;
  bool visible;
  _DisplayItem(this.label, this.visible);
}
