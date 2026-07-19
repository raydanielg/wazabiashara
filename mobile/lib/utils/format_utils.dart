import 'package:intl/intl.dart';

class FormatUtils {
  static String currency(num amount) {
    final fmt = NumberFormat('#,##0', 'en_US');
    return 'TZS ${fmt.format(amount)}';
  }

  static String currencyShort(num amount) {
    if (amount >= 1000000) {
      return 'TZS ${(amount / 1000000).toStringAsFixed(1)}M';
    } else if (amount >= 1000) {
      return 'TZS ${(amount / 1000).toStringAsFixed(1)}K';
    }
    return 'TZS ${NumberFormat('#,##0', 'en_US').format(amount)}';
  }

  static String date(DateTime date) {
    return DateFormat('dd/MM/yyyy').format(date);
  }

  static String dateTime(DateTime date) {
    return DateFormat('dd/MM/yyyy HH:mm').format(date);
  }

  static String time(DateTime date) {
    return DateFormat('HH:mm').format(date);
  }

  static String monthYear(DateTime date) {
    return DateFormat('MMMM yyyy').format(date);
  }

  static String relativeTime(DateTime date) {
    final now = DateTime.now();
    final diff = now.difference(date);

    if (diff.inMinutes < 1) return 'Just now';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
    if (diff.inHours < 24) return '${diff.inHours}h ago';
    if (diff.inDays < 7) return '${diff.inDays}d ago';
    return DateFormat('dd/MM/yyyy').format(date);
  }
}
