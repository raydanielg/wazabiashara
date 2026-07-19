import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';

/// Static "About this App" screen — app identity, version, and contact
/// links. Replaces the old "coming soon" snackbar stub.
class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  Future<void> _open(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('About this App')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Center(
            child: Column(
              children: [
                Container(
                  width: 84,
                  height: 84,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: AppColors.primaryGradient, begin: Alignment.topLeft, end: Alignment.bottomRight),
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [BoxShadow(color: AppColors.primary.withValues(alpha: 0.3), blurRadius: 16, offset: const Offset(0, 6))],
                  ),
                  child: const Icon(Icons.storefront_rounded, color: Colors.white, size: 40),
                ),
                const SizedBox(height: 16),
                const Text('Wazabiashara', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
                const SizedBox(height: 4),
                Text('Version 1.0.0', style: TextStyle(fontSize: 13, color: context.textSecondaryColor)),
              ],
            ),
          ),
          const SizedBox(height: 28),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(16), border: Border.all(color: context.borderColor)),
            child: Text(
              'Wazabiashara — Your Business, In Your Hands. Manage sales, inventory, branches, debts and reports from your phone, backed by real-time sync with your Wazabiashara account.',
              style: TextStyle(fontSize: 13, height: 1.5, color: context.textSecondaryColor),
            ),
          ),
          const SizedBox(height: 20),
          Container(
            decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(16), border: Border.all(color: context.borderColor)),
            child: Column(
              children: [
                _row(context, Icons.language_outlined, 'Website', 'wazabiashara.co.tz', () => _open('https://wazabiashara.co.tz')),
                Divider(height: 1, indent: 56, color: context.borderColor),
                _row(context, Icons.email_outlined, 'Support Email', 'contact@wazabiashara.co.tz', () => _open('mailto:contact@wazabiashara.co.tz')),
                Divider(height: 1, indent: 56, color: context.borderColor),
                _row(context, Icons.privacy_tip_outlined, 'Privacy Policy', 'How we handle your data', () => _open('https://wazabiashara.co.tz/privacy')),
              ],
            ),
          ),
          const SizedBox(height: 20),
          Center(
            child: Text('© ${DateTime.now().year} Wazabiashara. All rights reserved.', style: TextStyle(fontSize: 11, color: context.textSecondaryColor)),
          ),
        ],
      ),
    );
  }

  Widget _row(BuildContext context, IconData icon, String title, String subtitle, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: AppColors.primary, size: 18),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  Text(subtitle, style: TextStyle(fontSize: 12, color: context.textSecondaryColor)),
                ],
              ),
            ),
            Icon(Icons.chevron_right, color: context.textSecondaryColor, size: 20),
          ],
        ),
      ),
    );
  }
}
