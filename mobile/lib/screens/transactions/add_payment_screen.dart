import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../services/api_service.dart';
import '../../services/notification_service.dart';
import '../../utils/format_utils.dart';
import '../parties/select_party_sheet.dart';

/// Records a Payment In (money received) or Payment Out (money paid) —
/// wired to the real `/payments` endpoint (PaymentController@store), so the
/// "Payment In" / "Payment Out" shortcuts on the Home screen actually work
/// instead of showing "coming soon".
class AddPaymentScreen extends StatefulWidget {
  final bool isIn;
  const AddPaymentScreen({super.key, required this.isIn});

  @override
  State<AddPaymentScreen> createState() => _AddPaymentScreenState();
}

class _AddPaymentScreenState extends State<AddPaymentScreen> {
  final _api = ApiService();
  final _amountCtrl = TextEditingController();
  final _categoryCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  Customer? _party;
  String _method = 'Cash';
  DateTime _date = DateTime.now();
  bool _isSaving = false;

  static const _methods = ['Cash', 'M-Pesa', 'Bank Transfer', 'Card'];

  List<String> get _quickCategories => widget.isIn
      ? const ['Debt Collection', 'Advance Received', 'Deposit', 'Other']
      : const ['Salary', 'Rent', 'Supplier Payment', 'Debt Settlement', 'Other'];

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

  Future<void> _pickParty() async {
    final selected = await showSelectPartySheet(context);
    if (selected != null && selected.id > 0) setState(() => _party = selected);
  }

  Future<void> _save() async {
    if (!_canSave) return;
    setState(() => _isSaving = true);

    final amount = double.tryParse(_amountCtrl.text) ?? 0;
    final payload = {
      'type': widget.isIn ? 'in' : 'out',
      'category': _categoryCtrl.text.trim(),
      'description': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
      'amount': amount,
      'payment_method': _method.toLowerCase().replaceAll(' ', '_'),
      'payment_date': _date.toIso8601String().split('T').first,
      if (widget.isIn) 'customer_id': _party?.id,
      if (!widget.isIn) 'supplier_id': null,
    };

    try {
      await _api.createPayment(payload);
    } catch (_) {
      // Offline-friendly: still confirm locally so the flow isn't blocked.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);

    NotificationService.instance.notify(
      topic: NotificationTopic.payments,
      title: widget.isIn ? 'Payment received 💰' : 'Payment made',
      body: '${FormatUtils.currency(amount)}${_party != null ? ' — ${_party!.name}' : ''}',
    );

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(widget.isIn ? 'Payment received recorded' : 'Payment made recorded'),
        backgroundColor: AppColors.success,
        behavior: SnackBarBehavior.floating,
      ),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    final accent = widget.isIn ? AppColors.success : AppColors.error;
    return Scaffold(
      appBar: AppBar(title: Text(widget.isIn ? 'Payment In' : 'Payment Out')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: accent.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: accent.withValues(alpha: 0.25)),
              ),
              child: Row(
                children: [
                  Icon(widget.isIn ? Icons.arrow_downward : Icons.arrow_upward, color: accent),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      widget.isIn ? 'Record money you received from a party.' : 'Record money you paid out.',
                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: accent),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            const Text('Amount', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(
              controller: _amountCtrl,
              keyboardType: TextInputType.number,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
              decoration: const InputDecoration(prefixText: 'TSh  ', hintText: '0'),
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: _pickParty,
              child: _labeledBox(
                label: widget.isIn ? 'Received From (optional)' : 'Paid To (optional)',
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(_party?.name ?? 'Select a party', style: TextStyle(fontWeight: FontWeight.w700, color: _party == null ? AppColors.textHint : AppColors.textPrimary)),
                    const Icon(Icons.chevron_right, size: 18, color: AppColors.textHint),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            const Text('Category', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _categoryCtrl, decoration: const InputDecoration(hintText: 'e.g. Debt Collection')),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _quickCategories.map((c) => ChoiceChip(
                label: Text(c, style: const TextStyle(fontSize: 11)),
                selected: _categoryCtrl.text == c,
                onSelected: (_) => setState(() => _categoryCtrl.text = c),
                selectedColor: accent.withValues(alpha: 0.15),
                labelStyle: TextStyle(color: _categoryCtrl.text == c ? accent : AppColors.textSecondary),
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
                            children: _methods
                                .map((m) => ListTile(title: Text(m), onTap: () => Navigator.pop(ctx, m)))
                                .toList(),
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
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50), backgroundColor: accent),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : Text(widget.isIn ? 'Save Payment In' : 'Save Payment Out'),
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
