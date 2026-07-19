import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_widget.dart';
import '../transactions/add_reminder_screen.dart';

/// Real reminders list — pulled from GET /reminders (ReminderController),
/// with swipe-to-delete against DELETE /reminders/{id}. Replaces the old
/// "Reminders — coming soon" quick tile on the More menu.
class RemindersScreen extends StatefulWidget {
  const RemindersScreen({super.key});

  @override
  State<RemindersScreen> createState() => _RemindersScreenState();
}

class _RemindersScreenState extends State<RemindersScreen> {
  final _api = ApiService();
  bool _isLoading = true;
  List<Map<String, dynamic>> _reminders = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getReminders();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _reminders = list.cast<Map<String, dynamic>>());
      } else {
        setState(() => _reminders = []);
      }
    } catch (_) {
      setState(() => _reminders = []);
    }
    setState(() => _isLoading = false);
  }

  Future<void> _delete(Map<String, dynamic> reminder) async {
    final id = reminder['id'] as int;
    setState(() => _reminders.removeWhere((r) => r['id'] == id));
    try {
      await _api.deleteReminder(id);
    } catch (_) {
      // If the delete failed server-side, a pull-to-refresh will bring it back.
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Reminders')),
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.gold,
        foregroundColor: Colors.white,
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const AddReminderScreen()));
          _load();
        },
        child: const Icon(Icons.add_alarm),
      ),
      body: _isLoading
          ? const LoadingWidget(message: 'Loading reminders...')
          : RefreshIndicator(
              onRefresh: _load,
              child: _reminders.isEmpty
                  ? ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: const [
                        SizedBox(height: 100),
                        EmptyState(icon: Icons.alarm_outlined, title: 'No reminders yet', subtitle: 'Tap the + button to set your first reminder.'),
                      ],
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: _reminders.length,
                      itemBuilder: (ctx, i) {
                        final r = _reminders[i];
                        final remindAt = DateTime.tryParse(r['remind_at']?.toString() ?? '');
                        final isPast = remindAt != null && remindAt.isBefore(DateTime.now());
                        return Dismissible(
                          key: ValueKey(r['id']),
                          direction: DismissDirection.endToStart,
                          background: Container(
                            alignment: Alignment.centerRight,
                            padding: const EdgeInsets.symmetric(horizontal: 20),
                            margin: const EdgeInsets.only(bottom: 10),
                            decoration: BoxDecoration(color: AppColors.error, borderRadius: BorderRadius.circular(14)),
                            child: const Icon(Icons.delete_outline, color: Colors.white),
                          ),
                          onDismissed: (_) => _delete(r),
                          child: Container(
                            margin: const EdgeInsets.only(bottom: 10),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            decoration: BoxDecoration(
                              color: context.cardBg,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(color: context.borderColor),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  width: 40, height: 40,
                                  decoration: BoxDecoration(
                                    color: (isPast ? AppColors.textHint : AppColors.gold).withValues(alpha: 0.12),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: Icon(Icons.alarm, color: isPast ? AppColors.textHint : AppColors.goldDark, size: 20),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(r['title']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                                      if ((r['message'] as String?)?.isNotEmpty ?? false)
                                        Text(r['message'].toString(), style: TextStyle(fontSize: 12, color: context.textSecondaryColor), maxLines: 1, overflow: TextOverflow.ellipsis),
                                      Text(
                                        remindAt != null ? FormatUtils.dateTime(remindAt) : '',
                                        style: TextStyle(fontSize: 11, color: isPast ? AppColors.error : context.textSecondaryColor, fontWeight: FontWeight.w600),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
            ),
    );
  }
}
