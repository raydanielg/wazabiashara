import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Contact Details" screen. Every action here opens
/// a real system intent (email, phone dialer, browser) — nothing is a stub.
class ContactDetailsScreen extends StatelessWidget {
  const ContactDetailsScreen({super.key});

  static Future<void> _open(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Contact Details')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Padding(
              padding: EdgeInsets.only(left: 4, bottom: 8),
              child: Text('Contact us on', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            ),
            _row(context, Icons.email_outlined, AppColors.textSecondary, 'contact@wazabiashara.co.tz', () => _open('mailto:contact@wazabiashara.co.tz')),
            const SizedBox(height: 20),
            const Padding(
              padding: EdgeInsets.only(left: 4, bottom: 8),
              child: Text('Follow us on', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            ),
            Container(
              decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(14), border: Border.all(color: context.borderColor)),
              child: Column(
                children: [
                  _socialRow(const Color(0xFF1877F2), Icons.facebook, 'Facebook', () => _open('https://facebook.com/wazabiashara')),
                  Divider(height: 1, indent: 56, color: context.borderColor),
                  _socialRow(const Color(0xFFE1306C), Icons.camera_alt_outlined, 'Instagram', () => _open('https://instagram.com/wazabiashara')),
                  Divider(height: 1, indent: 56, color: context.borderColor),
                  _socialRow(Colors.black, Icons.music_note_outlined, 'Tiktok', () => _open('https://tiktok.com/@wazabiashara')),
                ],
              ),
            ),
            const Spacer(),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => _open('tel:+255700000000'),
                    style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                    child: const Text('Request Callback'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () => _open('mailto:contact@wazabiashara.co.tz?subject=Wazabiashara%20Feedback'),
                    style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                    child: const Text('Give Feedback'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(BuildContext context, IconData icon, Color color, String text, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(14), border: Border.all(color: context.borderColor)),
        child: Row(
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(width: 12),
            Expanded(child: Text(text, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
            const Icon(Icons.chevron_right, color: AppColors.textHint),
          ],
        ),
      ),
    );
  }

  Widget _socialRow(Color bg, IconData icon, String label, VoidCallback onTap) {
    return ListTile(
      leading: CircleAvatar(radius: 16, backgroundColor: bg, child: Icon(icon, size: 16, color: Colors.white)),
      title: Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
      trailing: const Icon(Icons.chevron_right, color: AppColors.textHint),
      onTap: onTap,
    );
  }
}
