import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Frequently Asked Questions" screen. Each topic
/// expands into real, Wazabiashara-specific answers instead of doing nothing.
class FaqScreen extends StatelessWidget {
  const FaqScreen({super.key});

  static const _topics = [
    (
      'App Overview',
      'Information about app',
      'Wazabiashara helps you run your business from your phone: record sales, track stock across branches, manage customer and supplier debts, and see real-time reports — all synced with your Wazabiashara account, so the same data shows up whether you use the app or the web dashboard.',
    ),
    (
      'App Features',
      'Various app functionalities',
      'Quick POS and Quick Entry for fast sales, Payment In/Out, Purchases, Expenses and Other Income tracking, a Notebook for quick notes, reminders with notifications, stock adjustments, and reports broken down by day, week or month.',
    ),
    (
      'Account & Billing',
      'Subscriptions, plans and payments',
      'Your Wazabiashara account is tied to the business you registered. For questions about your plan or billing, reach out from the Contact Details screen (Help & Support → Contact Us) and our team will help directly.',
    ),
    (
      'Data & Backup',
      'Keeping your business data safe',
      'Wazabiashara is server-backed: every sale, item and payment you record is saved straight to your account the moment you save it — there\'s no separate backup step. See More → Backup Information for a live summary of what\'s stored for your business.',
    ),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Frequently Asked Questions')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Container(
          decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(14), border: Border.all(color: context.borderColor)),
          child: Column(
            children: [
              for (int i = 0; i < _topics.length; i++) ...[
                ListTile(
                  leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(10)), child: const Icon(Icons.description_outlined, color: AppColors.primary, size: 18)),
                  title: Text(_topics[i].$1, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  subtitle: Text(_topics[i].$2, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                  trailing: const Icon(Icons.chevron_right, color: AppColors.textHint),
                  onTap: () => _showAnswer(context, _topics[i].$1, _topics[i].$3),
                ),
                if (i != _topics.length - 1) Divider(height: 1, indent: 64, color: context.borderColor),
              ],
            ],
          ),
        ),
      ),
    );
  }

  void _showAnswer(BuildContext context, String title, String answer) {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
            const SizedBox(height: 12),
            Text(answer, style: TextStyle(fontSize: 14, height: 1.5, color: ctx.textSecondaryColor)),
            const SizedBox(height: 20),
            SizedBox(width: double.infinity, child: OutlinedButton(onPressed: () => Navigator.pop(ctx), child: const Text('Close'))),
          ],
        ),
      ),
    );
  }
}
