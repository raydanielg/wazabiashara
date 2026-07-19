import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/theme_provider.dart';

/// Matches the reference "App Settings" screen.
class AppSettingsScreen extends StatefulWidget {
  const AppSettingsScreen({super.key});

  @override
  State<AppSettingsScreen> createState() => _AppSettingsScreenState();
}

class _AppSettingsScreenState extends State<AppSettingsScreen> {
  String _fontSize = 'Normal';
  String _currency = 'TSh';
  String _currencyPosition = 'Start';
  String _dateFormat = 'dd MMM yyyy';
  String _timeFormat = '12h';
  String _numberFormat = '1,000,000';
  bool _privacyMode = false;
  bool _appLock = false;

  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('App Settings')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _card([
            _navRow('Appearance', theme.isDarkMode ? 'Dark Mode' : 'Light Mode', () => theme.toggleDarkMode()),
            const Divider(height: 1, indent: 16),
            _navRow('Font Size', _fontSize, () => _pickOption('Font Size', ['Small', 'Normal', 'Large'], _fontSize, (v) => setState(() => _fontSize = v))),
            const Divider(height: 1, indent: 16),
            _navRow('Language', theme.language == 'sw' ? 'Kiswahili' : 'English', () => _pickOption('Language', ['English', 'Kiswahili'], theme.language == 'sw' ? 'Kiswahili' : 'English', (v) => theme.setLanguage(v == 'Kiswahili' ? 'sw' : 'en'))),
            const Divider(height: 1, indent: 16),
            _navRow('Currency', _currency, () => _pickOption('Currency', ['TSh', 'USD', 'KES', 'UGX'], _currency, (v) => setState(() => _currency = v))),
            _navRow('Currency Position', _currencyPosition, () => _pickOption('Currency Position', ['Start', 'End'], _currencyPosition, (v) => setState(() => _currencyPosition = v)), indent: 24),
            _navRow('Date Format', _dateFormat, () => _pickOption('Date Format', ['dd MMM yyyy', 'dd/MM/yyyy', 'MM/dd/yyyy'], _dateFormat, (v) => setState(() => _dateFormat = v)), indent: 24),
            _navRow('Time Format', _timeFormat == '12h' ? '12-hour' : '24-hour', () => _pickOption('Time Format', ['12h', '24h'], _timeFormat, (v) => setState(() => _timeFormat = v)), indent: 24),
            _navRow('Number Format', _numberFormat, () => _pickOption('Number Format', ['1,000,000', '1.000.000', '10,00,000'], _numberFormat, (v) => setState(() => _numberFormat = v)), indent: 24),
            const Divider(height: 1, indent: 16),
            _toggleRow('Privacy Mode', _privacyMode, (v) => setState(() => _privacyMode = v)),
            const Divider(height: 1, indent: 16),
            _toggleRow('App Lock', _appLock, (v) => setState(() => _appLock = v)),
          ]),
        ],
      ),
    );
  }

  Widget _card(List<Widget> children) => Container(
        decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
        child: Column(children: children),
      );

  Widget _navRow(String title, String value, VoidCallback onTap, {double indent = 16}) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: EdgeInsets.fromLTRB(indent, 12, 16, 12),
        child: Row(
          children: [
            Expanded(child: Text(title, style: TextStyle(fontWeight: indent > 16 ? FontWeight.w500 : FontWeight.w700, fontSize: indent > 16 ? 13 : 14))),
            Text(value, style: const TextStyle(color: AppColors.textSecondary)),
            const SizedBox(width: 4),
            const Icon(Icons.chevron_right, color: AppColors.textHint, size: 20),
          ],
        ),
      ),
    );
  }

  Widget _toggleRow(String title, bool value, ValueChanged<bool> onChanged) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      child: Row(
        children: [
          Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14))),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }

  void _pickOption(String title, List<String> options, String current, ValueChanged<String> onSelected) {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Padding(padding: const EdgeInsets.all(16), child: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800))),
          ...options.map((o) => ListTile(
                title: Text(o),
                trailing: current == o ? const Icon(Icons.check, color: AppColors.primary) : null,
                onTap: () {
                  onSelected(o);
                  Navigator.pop(ctx);
                },
              )),
          const SizedBox(height: 8),
        ],
      ),
    );
  }
}
