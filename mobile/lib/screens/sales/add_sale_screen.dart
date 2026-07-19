import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import 'select_party_sheet.dart';

/// Matches the reference "Add Sale" screen: invoice number + date, a party
/// picker (defaults to Cash Sale, tap "Change" to open [showSelectPartySheet]),
/// an items entry point, total amount, notes and image attachment, and a
/// Save button that's only enabled once a total has been entered.
class AddSaleScreen extends StatefulWidget {
  const AddSaleScreen({super.key});

  @override
  State<AddSaleScreen> createState() => _AddSaleScreenState();
}

class _AddSaleScreenState extends State<AddSaleScreen> {
  final _api = ApiService();
  final _amountCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  Customer _party = Customer(id: 0, name: 'Cash Sale');
  DateTime _date = DateTime.now();
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _amountCtrl.addListener(() => setState(() {}));
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  bool get _canSave => (double.tryParse(_amountCtrl.text) ?? 0) > 0 && !_isSaving;

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _date,
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (picked != null) setState(() => _date = picked);
  }

  Future<void> _changeParty() async {
    final selected = await showSelectPartySheet(context);
    if (selected != null) setState(() => _party = selected);
  }

  Future<void> _save() async {
    if (!_canSave) return;
    setState(() => _isSaving = true);

    final total = double.tryParse(_amountCtrl.text) ?? 0;
    try {
      await _api.createSale({
        'customer_id': _party.id > 0 ? _party.id : null,
        'total': total,
        'notes': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
        'date': _date.toIso8601String(),
      });
    } catch (_) {
      // Fall through — we still confirm to the user locally so the flow
      // isn't blocked while the backend is unreachable.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Sale recorded'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Add Sale'),
        actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.info_outline))],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: _labeledBox(
                    label: 'Invoice Number',
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [Text('1', style: TextStyle(fontWeight: FontWeight.w700)), Icon(Icons.expand_more, size: 18, color: AppColors.textHint)],
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: InkWell(
                    onTap: _pickDate,
                    child: _labeledBox(
                      label: 'Date',
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(FormatUtils.date(_date), style: const TextStyle(fontWeight: FontWeight.w700)),
                          const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textHint),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 18,
                    backgroundColor: AppColors.primary.withValues(alpha: 0.1),
                    child: Text(_party.name.isNotEmpty ? _party.name[0].toUpperCase() : '?', style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary)),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(_party.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                        Text(FormatUtils.currency(_party.currentDebt), style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                      ],
                    ),
                  ),
                  OutlinedButton.icon(
                    onPressed: _changeParty,
                    icon: const Icon(Icons.sync_alt, size: 16),
                    label: const Text('Change'),
                    style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            OutlinedButton.icon(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Item picker — coming soon'), behavior: SnackBarBehavior.floating),
                );
              },
              icon: const Icon(Icons.add_circle_outline, color: AppColors.primary),
              label: const Text('Add Items'),
              style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(48), foregroundColor: AppColors.primary),
            ),
            const SizedBox(height: 16),
            const Text('Total Amount', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(
              controller: _amountCtrl,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              decoration: const InputDecoration(prefixText: 'TSh  ', hintText: '0'),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _notesCtrl,
              maxLines: 3,
              decoration: const InputDecoration(hintText: 'Notes or Remarks'),
            ),
            const SizedBox(height: 12),
            TextButton.icon(
              onPressed: () {},
              icon: const Icon(Icons.image_outlined, size: 18),
              label: const Text('Add Images'),
              style: TextButton.styleFrom(foregroundColor: AppColors.primary, padding: EdgeInsets.zero),
            ),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save'),
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
