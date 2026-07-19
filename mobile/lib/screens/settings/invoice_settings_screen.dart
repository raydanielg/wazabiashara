import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Invoice Print Settings" screen.
class InvoiceSettingsScreen extends StatefulWidget {
  const InvoiceSettingsScreen({super.key});

  @override
  State<InvoiceSettingsScreen> createState() => _InvoiceSettingsScreenState();
}

class _InvoiceSettingsScreenState extends State<InvoiceSettingsScreen> {
  String _printType = 'thermal';
  String _pageSize = '58 mm';
  bool _showPhone = true;
  bool _showAddress = true;
  bool _showEmail = true;
  bool _showSignature = false;
  bool _showPartyBalance = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Invoice Print Settings')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Row(
            children: const [
              Text('Select Default Print Type', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
              SizedBox(width: 6),
              Icon(Icons.info_outline, size: 15, color: AppColors.textHint),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(child: _printTypeCard('regular', Icons.inventory_2_outlined, 'Regular')),
              const SizedBox(width: 12),
              Expanded(child: _printTypeCard('thermal', Icons.receipt_long, 'Thermal')),
            ],
          ),
          const SizedBox(height: 24),
          _sectionTitle('Printer Settings'),
          const SizedBox(height: 10),
          _card([
            _navRow('Setup Thermal Printer', null, () {}),
            const Divider(height: 1, indent: 16),
            _navRow('Page Size', _pageSize, () => _pickPageSize()),
          ]),
          const SizedBox(height: 24),
          _sectionTitle('Invoices'),
          const SizedBox(height: 10),
          _card([
            _navRow('Signature', null, () {}),
            const Divider(height: 1, indent: 16),
            _navRow('Terms & Conditions', null, () {}),
          ]),
          const SizedBox(height: 24),
          _sectionTitle('Invoice Customization'),
          const SizedBox(height: 10),
          _card([
            _toggleRow('Show Phone No. on Invoice', _showPhone, (v) => setState(() => _showPhone = v)),
            const Divider(height: 1, indent: 16),
            _toggleRow('Show Address on Invoice', _showAddress, (v) => setState(() => _showAddress = v)),
            const Divider(height: 1, indent: 16),
            _toggleRow('Show Email on Invoice', _showEmail, (v) => setState(() => _showEmail = v)),
            const Divider(height: 1, indent: 16),
            _toggleRow('Show Signature on Invoice', _showSignature, (v) => setState(() => _showSignature = v)),
            const Divider(height: 1, indent: 16),
            _toggleRow('Show Party Balance on Invoice', _showPartyBalance, (v) => setState(() => _showPartyBalance = v)),
          ]),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  Widget _printTypeCard(String value, IconData icon, String label) {
    final selected = _printType == value;
    return InkWell(
      onTap: () => setState(() => _printType = value),
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary.withValues(alpha: 0.06) : AppColors.surface,
          border: Border.all(color: selected ? AppColors.primary : AppColors.divider, width: selected ? 1.5 : 1),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(
          children: [
            Icon(icon, color: selected ? AppColors.primary : AppColors.textSecondary),
            const SizedBox(height: 8),
            Text(label, style: TextStyle(fontWeight: FontWeight.w700, color: selected ? AppColors.primary : AppColors.textPrimary)),
          ],
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) =>
      Text(title, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary, letterSpacing: 0.4));

  Widget _card(List<Widget> children) => Container(
        decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
        child: Column(children: children),
      );

  Widget _navRow(String title, String? value, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
            if (value != null) Padding(padding: const EdgeInsets.only(right: 6), child: Text(value, style: const TextStyle(color: AppColors.textSecondary))),
            const Icon(Icons.chevron_right, color: AppColors.textHint),
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
          Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }

  void _pickPageSize() {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: ['58 mm', '80 mm', 'A4'].map((s) => ListTile(
              title: Text(s),
              trailing: _pageSize == s ? const Icon(Icons.check, color: AppColors.primary) : null,
              onTap: () {
                setState(() => _pageSize = s);
                Navigator.pop(ctx);
              },
            )).toList(),
      ),
    );
  }
}
