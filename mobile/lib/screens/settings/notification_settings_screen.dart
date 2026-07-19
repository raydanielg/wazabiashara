import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/notification_service.dart';

/// Lets the user customize exactly which events push a notification —
/// Sales, Payments, Low Stock, Reminders — each independently switchable.
class NotificationSettingsScreen extends StatefulWidget {
  const NotificationSettingsScreen({super.key});

  @override
  State<NotificationSettingsScreen> createState() => _NotificationSettingsScreenState();
}

class _NotificationSettingsScreenState extends State<NotificationSettingsScreen> {
  final _service = NotificationService.instance;
  bool _isLoading = true;

  final Map<NotificationTopic, bool> _values = {};

  static const _topics = [
    (NotificationTopic.sales, Icons.point_of_sale_outlined, AppColors.success, 'Sales', 'Get notified the moment a sale is completed'),
    (NotificationTopic.payments, Icons.payments_outlined, AppColors.info, 'Payments', 'Payment In / Payment Out confirmations'),
    (NotificationTopic.lowStock, Icons.inventory_2_outlined, AppColors.warning, 'Low Stock', 'Alerts when an item drops below its reorder level'),
    (NotificationTopic.reminders, Icons.alarm_outlined, AppColors.gold, 'Reminders', 'Reminders you set inside the app'),
  ];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    for (final t in _topics) {
      _values[t.$1] = await _service.isEnabled(t.$1);
    }
    setState(() => _isLoading = false);
  }

  Future<void> _toggle(NotificationTopic topic, bool value) async {
    setState(() => _values[topic] = value);
    await _service.setEnabled(topic, value);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notifications')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(strokeWidth: 2))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withValues(alpha: 0.06),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.notifications_active_outlined, size: 18, color: AppColors.primary),
                        SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            'Choose exactly what Wazabiashara should notify you about.',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.primary),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  Container(
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: AppColors.divider),
                    ),
                    child: Column(
                      children: [
                        for (int i = 0; i < _topics.length; i++) ...[
                          _topicTile(_topics[i]),
                          if (i < _topics.length - 1) const Divider(height: 1, indent: 56),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  TextButton.icon(
                    onPressed: () => NotificationService.instance.requestPermission(),
                    icon: const Icon(Icons.settings_outlined, size: 16),
                    label: const Text('Re-check device notification permission'),
                    style: TextButton.styleFrom(foregroundColor: AppColors.textSecondary),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _topicTile((NotificationTopic, IconData, Color, String, String) topic) {
    final (id, icon, color, title, subtitle) = topic;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Switch(
            value: _values[id] ?? true,
            onChanged: (v) => _toggle(id, v),
            activeColor: AppColors.primary,
          ),
        ],
      ),
    );
  }
}
