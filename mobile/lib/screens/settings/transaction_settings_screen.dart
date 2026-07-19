import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Transaction Settings" screen.
class TransactionSettingsScreen extends StatefulWidget {
  const TransactionSettingsScreen({super.key});

  @override
  State<TransactionSettingsScreen> createState() => _TransactionSettingsScreenState();
}

class _TransactionSettingsScreenState extends State<TransactionSettingsScreen> {
  bool _cashSaleDefault = false;
  bool _dueDateReminder = false;
  bool _otherIncome = true;
  bool _transactionPrefixes = false;
  bool _additionalCharges = true;
  bool _roundOff = false;
  bool _saveImages = false;
  bool _imageCropping = false;
  String _reminderLanguage = 'English';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Transaction Settings')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _card([
            _toggle('Set Cash Sale by Default', 'By enabling this setting, the every transaction will be recorded as cash transaction.', _cashSaleDefault, (v) => setState(() => _cashSaleDefault = v)),
            const Divider(height: 1),
            _toggle('Enable Due Date Reminder', 'You can record the due collection date for unpaid invoices.', _dueDateReminder, (v) => setState(() => _dueDateReminder = v)),
            const Divider(height: 1),
            _toggle('Enable Other Income Transactions', 'By enabling this you can add income transaction.', _otherIncome, (v) => setState(() => _otherIncome = v)),
            const Divider(height: 1),
            _toggle('Enable Transaction Prefixes', 'By enabling prefix, you can manage invoice with multiple number.', _transactionPrefixes, (v) => setState(() => _transactionPrefixes = v)),
          ]),
          const SizedBox(height: 20),
          const Padding(
            padding: EdgeInsets.only(left: 4, bottom: 8),
            child: Text('Others Settings', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
          ),
          _card([
            _toggle('Enable Additional Charges', null, _additionalCharges, (v) => setState(() => _additionalCharges = v)),
            const Divider(height: 1),
            _toggle('Enable Round off', null, _roundOff, (v) => setState(() => _roundOff = v)),
            const Divider(height: 1),
            _toggle('Save Images in Gallery', null, _saveImages, (v) => setState(() => _saveImages = v)),
            const Divider(height: 1),
            _toggle('Enable Image Cropping', null, _imageCropping, (v) => setState(() => _imageCropping = v)),
            const Divider(height: 1),
            InkWell(
              onTap: _pickLanguage,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                child: Row(
                  children: [
                    const Expanded(child: Text('Reminder Message Language', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
                    Text(_reminderLanguage, style: const TextStyle(color: AppColors.textSecondary)),
                    const Icon(Icons.chevron_right, color: AppColors.textHint),
                  ],
                ),
              ),
            ),
          ]),
        ],
      ),
    );
  }

  Widget _card(List<Widget> children) => Container(
        margin: const EdgeInsets.only(bottom: 4),
        decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
        child: Column(children: children),
      );

  Widget _toggle(String title, String? subtitle, bool value, ValueChanged<bool> onChanged) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                if (subtitle != null) ...[
                  const SizedBox(height: 2),
                  Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                ],
              ],
            ),
          ),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }

  void _pickLanguage() {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: ['English', 'Kiswahili'].map((s) => ListTile(
              title: Text(s),
              trailing: _reminderLanguage == s ? const Icon(Icons.check, color: AppColors.primary) : null,
              onTap: () {
                setState(() => _reminderLanguage = s);
                Navigator.pop(ctx);
              },
            )).toList(),
      ),
    );
  }
}
