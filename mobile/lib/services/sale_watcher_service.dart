import 'dart:async';
import 'api_service.dart';
import 'notification_service.dart';
import 'storage_service.dart';
import '../utils/format_utils.dart';

/// Best-effort "background" sale watcher — since true OS-level background
/// execution needs native scheduling (WorkManager / BGTaskScheduler) that
/// can't be wired up without a full native build in this environment, this
/// instead polls `/sales` on a timer while the app is open (foreground or
/// resumed), so a sale made from *another device or staff member's phone*
/// still surfaces a notification here shortly after it happens — the app
/// doesn't need to be the one that created it.
///
/// Sales made from *this* device already notify immediately at the point of
/// sale (see AddSaleScreen / PosScreen) — [markSeen] is called right after
/// those so the next poll doesn't notify twice for the same sale.
class SaleWatcherService {
  SaleWatcherService._internal();
  static final SaleWatcherService instance = SaleWatcherService._internal();

  static const _lastSeenKey = 'sale_watcher_last_seen_id';
  static const _pollInterval = Duration(seconds: 45);

  final _api = ApiService();
  final _storage = StorageService();
  Timer? _timer;
  bool _isPolling = false;

  bool get isRunning => _timer != null;

  void start() {
    if (_timer != null) return;
    _tick();
    _timer = Timer.periodic(_pollInterval, (_) => _tick());
  }

  void stop() {
    _timer?.cancel();
    _timer = null;
  }

  /// Call right after a sale is created from within this app, so the
  /// watcher doesn't re-announce it on the next poll.
  Future<void> markSeen(int? saleId) async {
    if (saleId == null) return;
    final currentId = int.tryParse(await _storage.getString(_lastSeenKey) ?? '') ?? 0;
    if (saleId > currentId) {
      await _storage.setString(_lastSeenKey, saleId.toString());
    }
  }

  Future<void> _tick() async {
    if (_isPolling) return;
    _isPolling = true;
    try {
      final res = await _api.getSales();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final raw = (res.data['data'] as List?) ?? [];
        if (raw.isNotEmpty) {
          final sales = List<Map<String, dynamic>>.from(raw);
          sales.sort((a, b) => (b['id'] as int).compareTo(a['id'] as int));
          final latestId = sales.first['id'] as int;

          final storedRaw = await _storage.getString(_lastSeenKey);
          final lastSeenId = int.tryParse(storedRaw ?? '');

          if (lastSeenId == null) {
            // First run this session: just set the baseline — don't spam
            // notifications for sales that already existed before we opened.
            await _storage.setString(_lastSeenKey, latestId.toString());
          } else if (latestId > lastSeenId) {
            final newOnes = sales.where((s) => (s['id'] as int) > lastSeenId).toList()
              ..sort((a, b) => (a['id'] as int).compareTo(b['id'] as int));
            for (final s in newOnes) {
              final total = (s['total'] as num?)?.toDouble() ?? 0;
              final receipt = s['receipt_no'] as String? ?? '';
              await NotificationService.instance.notify(
                topic: NotificationTopic.sales,
                title: 'New sale recorded 🎉',
                body: receipt.isNotEmpty ? '$receipt — ${FormatUtils.currency(total)}' : FormatUtils.currency(total),
              );
            }
            await _storage.setString(_lastSeenKey, latestId.toString());
          }
        }
      }
    } catch (_) {
      // Offline — just try again next tick.
    } finally {
      _isPolling = false;
    }
  }
}
