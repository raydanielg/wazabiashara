import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'faq_screen.dart';
import 'tutorial_videos_screen.dart';
import 'contact_details_screen.dart';
import 'tickets_screen.dart';

/// Hub screen linking to FAQ, Tutorial Videos, Contact Details and the
/// Report a Problem / Tickets flow.
class HelpSupportScreen extends StatelessWidget {
  const HelpSupportScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Help and Support')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
            child: Column(
              children: [
                _row(context, Icons.quiz_outlined, AppColors.primary, 'Frequently Asked Questions', const FaqScreen()),
                const Divider(height: 1, indent: 56),
                _row(context, Icons.play_circle_outline, AppColors.info, 'Tutorial Videos', const TutorialVideosScreen()),
                const Divider(height: 1, indent: 56),
                _row(context, Icons.contact_support_outlined, AppColors.success, 'Contact Details', const ContactDetailsScreen()),
                const Divider(height: 1, indent: 56),
                _row(context, Icons.flag_outlined, AppColors.warning, 'Report a Problem', const TicketsScreen()),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _row(BuildContext context, IconData icon, Color color, String title, Widget screen) {
    return ListTile(
      leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)), child: Icon(icon, color: color, size: 18)),
      title: Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
      trailing: const Icon(Icons.chevron_right, color: AppColors.textHint),
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => screen)),
    );
  }
}
