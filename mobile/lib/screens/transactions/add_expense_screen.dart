import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';

/// Records a business Expense — wired to the real `/expenses` endpoint
/// (ExpenseController@store), so the "Expense" shortcut actually works.
class AddExpenseScreen extends StatefulWidget {
  const AddExpenseScreen({super.key});

  @override
  State<AddExpenseScreen> createState() => _AddExpenseScreenState();
}

class _AddExpenseScreenState extends State<AddExpenseScreen> {
  final _api = ApiService();
  final _amountCtrl = TextEditingController();
  final _categoryCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  DateTime _date = DateTime.now();
  bool _isSaving = false;

  static const _quickCategories = ['Rent', 'Utilities', 'Transport', 'Salaries', 'Supplies', 'Maintenance', 'Other'];

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
      await _api.createExpense({
        'category': _categoryCtrl.text.trim(),
        'description': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'expense_date': _date.toIso8601String().split('T').first,
      });
    } catch (_) {}

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Expense recorded'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Add Expense')),
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
            TextField(controller: _categoryCtrl, decoration: const InputDecoration(hintText: 'e.g. Rent')),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _quickCategories.map((c) => ChoiceChip(
                label: Text(c, style: const TextStyle(fontSize: 11)),
                selected: _categoryCtrl.text == c,
                onSelected: (_) => setState(() => _categoryCtrl.text = c),
                selectedColor: AppColors.error.withValues(alpha: 0.12),
                labelStyle: TextStyle(color: _categoryCtrl.text == c ? AppColors.error : AppColors.textSecondary),
              )).toList(),
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: _pickDate,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Date', style: TextStyle(fontSize: 11, color: AppColors.textSecondary)),
                    const SizedBox(height: 4),
                    Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Text(FormatUtils.date(_date), style: const TextStyle(fontWeight: FontWeight.w700)),
                      const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textHint),
                    ]),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            TextField(controller: _notesCtrl, maxLines: 3, decoration: const InputDecoration(hintText: 'Notes or Remarks')),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50), backgroundColor: AppColors.error),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save Expense'),
            ),
          ],
        ),
      ),
    );
  }
}
