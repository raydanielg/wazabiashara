import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'storage_service.dart';

enum NotificationTopic { sales, payments, lowStock, reminders }

/// Wraps flutter_local_notifications with Wazabiashara's own channels
/// (Sales, Payments, Low Stock, Reminders) and a simple per-topic on/off
/// preference (see NotificationSettingsScreen) so notifications stay
/// customizable per the user's request ("niwe na custom notification").
class NotificationService {
  NotificationService._internal();
  static final NotificationService instance = NotificationService._internal();

  final _plugin = FlutterLocalNotificationsPlugin();
  final _storage = StorageService();
  bool _initialized = false;
  int _idCounter = 0;

  static const _channelSales = AndroidNotificationChannel(
    'sales_channel',
    'Sales',
    description: 'Notified whenever a sale is completed',
    importance: Importance.high,
  );
  static const _channelPayments = AndroidNotificationChannel(
    'payments_channel',
    'Payments',
    description: 'Notified for payments received or paid out',
    importance: Importance.high,
  );
  static const _channelStock = AndroidNotificationChannel(
    'stock_channel',
    'Low Stock',
    description: 'Alerts when an item runs low on stock',
    importance: Importance.defaultImportance,
  );
  static const _channelReminders = AndroidNotificationChannel(
    'reminders_channel',
    'Reminders',
    description: 'Reminders you set inside the app',
    importance: Importance.high,
  );

  static const keySales = 'notif_sales_enabled';
  static const keyPayments = 'notif_payments_enabled';
  static const keyStock = 'notif_stock_enabled';
  static const keyReminders = 'notif_reminders_enabled';

  /// Call once at app startup — sets up notification channels. Safe to call
  /// before the user is signed in.
  Future<void> init() async {
    if (_initialized) return;

    const androidInit = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosInit = DarwinInitializationSettings(
      requestAlertPermission: false,
      requestBadgePermission: false,
      requestSoundPermission: false,
    );
    const settings = InitializationSettings(android: androidInit, iOS: iosInit);

    try {
      await _plugin.initialize(settings);
      final androidImpl = _plugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      for (final channel in [_channelSales, _channelPayments, _channelStock, _channelReminders]) {
        await androidImpl?.createNotificationChannel(channel);
      }
      _initialized = true;
    } catch (_) {
      // Notifications are a nice-to-have — never block app startup on this.
    }
  }

  /// Requests the OS-level notification permission (Android 13+, iOS). Best
  /// called once, right after the user lands on the dashboard for the first
  /// time in a session — calling it again is harmless (returns the cached
  /// OS decision).
  Future<void> requestPermission() async {
    try {
      final androidImpl = _plugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      await androidImpl?.requestNotificationsPermission();

      final iosImpl = _plugin.resolvePlatformSpecificImplementation<IOSFlutterLocalNotificationsPlugin>();
      await iosImpl?.requestPermissions(alert: true, badge: true, sound: true);
    } catch (_) {}
  }

  Future<bool> isEnabled(NotificationTopic topic) async {
    return await _storage.getBool(_keyFor(topic)) ?? true; // opt-out, not opt-in
  }

  Future<void> setEnabled(NotificationTopic topic, bool value) async {
    await _storage.setBool(_keyFor(topic), value);
  }

  String _keyFor(NotificationTopic topic) {
    switch (topic) {
      case NotificationTopic.sales:
        return keySales;
      case NotificationTopic.payments:
        return keyPayments;
      case NotificationTopic.lowStock:
        return keyStock;
      case NotificationTopic.reminders:
        return keyReminders;
    }
  }

  AndroidNotificationChannel _channelFor(NotificationTopic topic) {
    switch (topic) {
      case NotificationTopic.sales:
        return _channelSales;
      case NotificationTopic.payments:
        return _channelPayments;
      case NotificationTopic.lowStock:
        return _channelStock;
      case NotificationTopic.reminders:
        return _channelReminders;
    }
  }

  /// Shows a local notification for [topic] — silently does nothing if the
  /// user has turned that topic off in Notification Settings.
  Future<void> notify({
    required NotificationTopic topic,
    required String title,
    required String body,
  }) async {
    if (!_initialized) await init();
    if (!await isEnabled(topic)) return;

    final channel = _channelFor(topic);
    final details = NotificationDetails(
      android: AndroidNotificationDetails(
        channel.id,
        channel.name,
        channelDescription: channel.description,
        importance: channel.importance,
        priority: Priority.high,
        icon: '@mipmap/ic_launcher',
      ),
      iOS: const DarwinNotificationDetails(presentAlert: true, presentBadge: true, presentSound: true),
    );

    try {
      _idCounter = (_idCounter + 1) % 100000;
      await _plugin.show(_idCounter, title, body, details);
    } catch (_) {
      // Never let a notification failure break the flow it's celebrating.
    }
  }
}
