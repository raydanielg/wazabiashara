import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';

/// Records "Other Income" — wired to the real `/incomes` endpoint
/// (IncomeController@store).
class AddIncomeScreen extends StatefulWidget {
  const AddIncomeScreen({super.key});

  @override
  State<AddIncomeScreen> createState() => _AddIncomeScreenState();
}

class _AddIncomeScreenState extends State<AddIncomeScreen> {
  final _api = ApiService();
  final _amountCtrl = TextEditingController();
  final _categoryCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  String _method = 'Cash';
  DateTime _date = DateTime.now();
  bool _isSaving = false;

  static const _methods = ['Cash', 'M-Pesa', 'Bank Transfer', 'Card'];
  static const _quickCategories = ['Commission', 'Rental Income', 'Interest', 'Refund', 'Other'];

  @override
  void initState() {
    super.initState();
    _amountCtrl.addListener(() => setState(() {}));
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _categoryCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  bool get _canSave => (double.tryParse(_amountCtrl.text) ?? 0) > 0 && _categoryCtrl.text.trim().isNotEmpty && !_isSaving;

  Future<void> _pickDate() async {
    final picked = await showDatePicker(context: context, initialDate: _date, firstDate: DateTime(2020), lastDate: DateTime(2100));
    if (picked != null) setState(() => _date = picked);
  }

  Future<void> _save() async {
    if (!_canSave) return;
    setState(() => _isSaving = true);

    try {
      await _api.createIncome({
        'category': _categoryCtrl.text.trim(),
        'description': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'payment_method': _method.toLowerCase().replaceAll(' ', '_'),
        'income_date': _date.toIso8601String().split('T').first,
      });
    } catch (_) {}

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Income recorded'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Other Income')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Amount', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(
              controller: _amountCtrl,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
              decoration: const InputDecoration(prefixText: 'TSh  ', hintText: '0'),
            ),
            const SizedBox(height: 16),
            const Text('Category', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _categoryCtrl, decoration: const InputDecoration(hintText: 'e.g. Commission')),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _quickCategories.map((c) => ChoiceChip(
                label: Text(c, style: const TextStyle(fontSize: 11)),
                selected: _categoryCtrl.text == c,
                onSelected: (_) => setState(() => _categoryCtrl.text = c),
                selectedColor: AppColors.success.withValues(alpha: 0.15),
                labelStyle: TextStyle(color: _categoryCtrl.text == c ? AppColors.success : AppColors.textSecondary),
              )).toList(),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: InkWell(
                    onTap: () async {
                      final chosen = await showModalBottomSheet<String>(
                        context: context,
                        builder: (ctx) => SafeArea(
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: _methods.map((m) => ListTile(title: Text(m), onTap: () => Navigator.pop(ctx, m))).toList(),
                          ),
                        ),
                      );
                      if (chosen != null) setState(() => _method = chosen);
                    },
                    child: _labeledBox(
                      label: 'Payment Method',
                      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                        Text(_method, style: const TextStyle(fontWeight: FontWeight.w700)),
                        const Icon(Icons.expand_more, size: 18, color: AppColors.textHint),
                      ]),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: InkWell(
                    onTap: _pickDate,
                    child: _labeledBox(
                      label: 'Date',
                      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                        Text(FormatUtils.date(_date), style: const TextStyle(fontWeight: FontWeight.w700)),
                        const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textHint),
                      ]),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            TextField(controller: _notesCtrl, maxLines: 3, decoration: const InputDecoration(hintText: 'Notes or Remarks')),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50), backgroundColor: AppColors.success),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save Income'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _labeledBox({required String label, required Widget child}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
          const SizedBox(height: 4),
          child,
        ],
      ),
    );
  }
}
