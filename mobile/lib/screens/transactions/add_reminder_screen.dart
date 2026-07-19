import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';

/// Quick "Add Reminder" flow — wired to the real `/reminders` endpoint
/// (ReminderController@store).
class AddReminderScreen extends StatefulWidget {
  const AddReminderScreen({super.key});

  @override
  State<AddReminderScreen> createState() => _AddReminderScreenState();
}

class _AddReminderScreenState extends State<AddReminderScreen> {
  final _api = ApiService();
  final _titleCtrl = TextEditingController();
  final _messageCtrl = TextEditingController();

  DateTime _date = DateTime.now().add(const Duration(days: 1));
  TimeOfDay _time = const TimeOfDay(hour: 9, minute: 0);
  String _channel = 'app';
  bool _isSaving = false;

  static const _channels = {'app': 'In-App', 'sms': 'SMS', 'whatsapp': 'WhatsApp', 'email': 'Email'};

  @override
  void initState() {
    super.initState();
    _titleCtrl.addListener(() => setState(() {}));
  }

  @override
  void dispose() {
    _titleCtrl.dispose();
    _messageCtrl.dispose();
    super.dispose();
  }

  bool get _canSave => _titleCtrl.text.trim().isNotEmpty && !_isSaving;

  Future<void> _pickDate() async {
    final picked = await showDatePicker(context: context, initialDate: _date, firstDate: DateTime.now(), lastDate: DateTime(2100));
    if (picked != null) setState(() => _date = picked);
  }

  Future<void> _pickTime() async {
    final picked = await showTimePicker(context: context, initialTime: _time);
    if (picked != null) setState(() => _time = picked);
  }

  Future<void> _save() async {
    if (!_canSave) return;
    setState(() => _isSaving = true);

    final remindAt = DateTime(_date.year, _date.month, _date.day, _time.hour, _time.minute);
    try {
      await _api.createReminder({
        'type': 'general',
        'title': _titleCtrl.text.trim(),
        'message': _messageCtrl.text.trim().isEmpty ? null : _messageCtrl.text.trim(),
        'channel': _channel,
        'remind_at': remindAt.toIso8601String(),
      });
    } catch (_) {}

    if (!mounted) return;
    setState(() => _isSaving = false);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Reminder set'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Add Reminder')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Title', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _titleCtrl, decoration: const InputDecoration(hintText: 'e.g. Follow up with supplier')),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: InkWell(
                    onTap: _pickDate,
                    child: _labeledBox(label: 'Date', child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Text(FormatUtils.date(_date), style: const TextStyle(fontWeight: FontWeight.w700)),
                      const Icon(Icons.calendar_today_outlined, size: 16, color: AppColors.textHint),
                    ])),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: InkWell(
                    onTap: _pickTime,
                    child: _labeledBox(label: 'Time', child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Text(_time.format(context), style: const TextStyle(fontWeight: FontWeight.w700)),
                      const Icon(Icons.access_time, size: 16, color: AppColors.textHint),
                    ])),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            const Text('Notify Via', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: _channels.entries.map((e) => ChoiceChip(
                label: Text(e.value, style: const TextStyle(fontSize: 11)),
                selected: _channel == e.key,
                onSelected: (_) => setState(() => _channel = e.key),
                selectedColor: AppColors.primary.withValues(alpha: 0.15),
                labelStyle: TextStyle(color: _channel == e.key ? AppColors.primary : AppColors.textSecondary),
              )).toList(),
            ),
            const SizedBox(height: 16),
            TextField(controller: _messageCtrl, maxLines: 3, decoration: const InputDecoration(hintText: 'Notes (optional)')),
            const SizedBox(height: 28),
            ElevatedButton(
              onPressed: _canSave ? _save : null,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Save Reminder'),
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
