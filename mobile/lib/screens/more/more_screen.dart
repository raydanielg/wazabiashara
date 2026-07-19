import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../routes/app_routes.dart';
import '../reports/reports_screen.dart';
import '../settings/settings_screen.dart';
import '../categories/category_list_screen.dart';
import '../accounts/cash_bank_screen.dart';
import '../support/help_support_screen.dart';
import '../calculators/emi_calculator_screen.dart';
import '../calculators/interest_calculator_screen.dart';
import '../calculators/tax_calculator_screen.dart';
import '../notebook/notebook_screen.dart';
import '../import/import_hub_screen.dart';
import '../settings/business_profile_screen.dart';
import '../settings/my_account_screen.dart';
import 'bill_gallery_screen.dart';
import 'backup_info_screen.dart';
import 'about_screen.dart';
import 'reminders_screen.dart';

/// The "More" tab — mirrors the menu structure of the reference app:
/// quick shortcuts, My Account, a Management section, a Utilities section
/// and an Others section. Screens that already exist in this codebase are
/// wired up; the rest show a friendly "coming soon" notice until they're
/// built.
class MoreScreen extends StatelessWidget {
  const MoreScreen({super.key});

  void _openCategories(BuildContext context, String type, String title) {
    Navigator.push(context, MaterialPageRoute(builder: (_) => CategoryListScreen(type: type, title: title)));
  }

  void _comingSoon(BuildContext context, String feature) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('$feature is on our roadmap — not built yet.'),
        backgroundColor: AppColors.primary,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  void _shareApp(BuildContext context) {
    const link = 'https://wazabiashara.co.tz';
    Clipboard.setData(const ClipboardData(text: 'Try Wazabiashara — manage your business from your phone: $link'));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Link copied — paste it anywhere to share'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final name = auth.user?.name ?? 'User';
    final role = auth.user?.role ?? 'User';

    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            CircleAvatar(
              radius: 16,
              backgroundColor: AppColors.primary.withValues(alpha: 0.1),
              child: Text(
                name.isNotEmpty ? name[0].toUpperCase() : 'U',
                style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary, fontSize: 13),
              ),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(name, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                Text(
                  role[0].toUpperCase() + role.substring(1),
                  style: const TextStyle(fontSize: 11, color: AppColors.textSecondary, fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ],
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Quick shortcuts row
            Row(
              children: [
                Expanded(child: _quickTile(context, Icons.card_giftcard_outlined, 'Greeting\nCards', () => _comingSoon(context, 'Greeting Cards'))),
                const SizedBox(width: 12),
                Expanded(child: _quickTile(context, Icons.badge_outlined, 'Business\nCard', () => _comingSoon(context, 'Business Card'))),
                const SizedBox(width: 12),
                Expanded(child: _quickTile(context, Icons.notifications_active_outlined, 'Reminders', () {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => const RemindersScreen()));
                })),
              ],
            ),
            const SizedBox(height: 20),

            _card(context, [
              _item(context, Icons.person_outline, AppColors.primary, 'My Account', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const MyAccountScreen()));
              }),
            ]),

            const SizedBox(height: 20),
            _sectionTitle('Management'),
            const SizedBox(height: 10),
            _card(context, [
              _item(context, Icons.store_outlined, AppColors.primary, 'Business Profile', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const BusinessProfileScreen()));
              }),
              _divider(),
              _item(context, Icons.account_balance_outlined, AppColors.info, 'Cash & Bank Accounts', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const CashBankScreen()));
              }),
              _divider(),
              _expandable(
                icon: Icons.category_outlined,
                iconColor: const Color(0xFF8B5CF6),
                title: 'Manage Categories',
                children: [
                  _subItem(context, 'Party Categories', () => _openCategories(context, 'party', 'Manage Party Categories')),
                  _subItem(context, 'Item Categories', () => _openCategories(context, 'item', 'Manage Item Categories')),
                  _subItem(context, 'Expense Categories', () => _openCategories(context, 'expense', 'Manage Expense Categories')),
                  _subItem(context, 'Income Categories', () => _openCategories(context, 'income', 'Manage Income Categories')),
                ],
              ),
              _divider(),
              _item(context, Icons.bar_chart_outlined, AppColors.success, 'View Reports', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const ReportsScreen()));
              }),
              _divider(),
              _item(context, Icons.settings_outlined, AppColors.textSecondary, 'Settings', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const SettingsScreen()));
              }),
            ]),

            const SizedBox(height: 20),
            _sectionTitle('Utilities'),
            const SizedBox(height: 10),
            _card(context, [
              _item(context, Icons.upload_file_outlined, AppColors.info, 'Import Data', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const ImportHubScreen()));
              }),
              _divider(),
              _item(context, Icons.photo_library_outlined, AppColors.gold, 'Bill Gallery', badge: 'New', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const BillGalleryScreen()));
              }),
              _divider(),
              _item(context, Icons.note_alt_outlined, AppColors.gold, 'Notebook', badge: 'New', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const NotebookScreen()));
              }),
              _divider(),
              _expandable(
                icon: Icons.calculate_outlined,
                iconColor: AppColors.warning,
                title: 'Calculators',
                children: [
                  _subItem(context, 'EMI Calculator', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const EmiCalculatorScreen()))),
                  _subItem(context, 'Interest Calculator', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const InterestCalculatorScreen()))),
                  _subItem(context, 'Tax Calculator', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const TaxCalculatorScreen()))),
                ],
              ),
            ]),

            const SizedBox(height: 20),
            _sectionTitle('Others'),
            const SizedBox(height: 10),
            _card(context, [
              _item(context, Icons.support_agent_outlined, AppColors.info, 'Help and Support', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const HelpSupportScreen()));
              }),
              _divider(),
              _item(context, Icons.cloud_outlined, AppColors.success, 'Backup Information', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const BackupInfoScreen()));
              }),
              _divider(),
              _item(context, Icons.info_outline, AppColors.primary, 'About this App', onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const AboutScreen()));
              }),
              _divider(),
              _item(context, Icons.share_outlined, AppColors.primary, 'Share this App', onTap: () => _shareApp(context)),
            ]),

            const SizedBox(height: 20),
            _card(context, [
              _item(context, Icons.logout, AppColors.error, 'Logout', isDestructive: true, onTap: () => _confirmLogout(context, auth)),
            ]),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _quickTile(BuildContext context, IconData icon, String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: context.cardBg,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: context.borderColor),
        ),
        child: Column(
          children: [
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: AppColors.primary, size: 20),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppColors.textPrimary, height: 1.2),
            ),
          ],
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) => Padding(
        padding: const EdgeInsets.only(left: 4),
        child: Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSecondary, letterSpacing: 0.5)),
      );

  Widget _card(BuildContext context, List<Widget> children) => Container(
        decoration: BoxDecoration(
          color: context.cardBg,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: context.borderColor),
        ),
        child: Column(children: children),
      );

  Widget _divider() => const Divider(height: 1, indent: 56);

  Widget _item(
    BuildContext context,
    IconData icon,
    Color iconColor,
    String title, {
    required VoidCallback onTap,
    String? badge,
    bool isDestructive = false,
  }) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: isDestructive ? AppColors.error.withValues(alpha: 0.1) : iconColor.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: isDestructive ? AppColors.error : iconColor, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                title,
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: isDestructive ? AppColors.error : AppColors.textPrimary),
              ),
            ),
            if (badge != null) ...[
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(color: AppColors.error, borderRadius: BorderRadius.circular(20)),
                child: Text(badge, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: Colors.white)),
              ),
              const SizedBox(width: 8),
            ],
            if (!isDestructive) const Icon(Icons.chevron_right, color: AppColors.textHint, size: 22),
          ],
        ),
      ),
    );
  }

  Widget _expandable({required IconData icon, required Color iconColor, required String title, required List<Widget> children}) {
    return Theme(
      data: ThemeData(dividerColor: Colors.transparent),
      child: ExpansionTile(
        tilePadding: const EdgeInsets.symmetric(horizontal: 16),
        childrenPadding: EdgeInsets.zero,
        leading: Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(color: iconColor.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
          child: Icon(icon, color: iconColor, size: 20),
        ),
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPrimary)),
        children: children,
      ),
    );
  }

  Widget _subItem(BuildContext context, String title, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.only(left: 64, right: 16, top: 12, bottom: 12),
        child: Row(
          children: [
            Expanded(child: Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textSecondary))),
            const Icon(Icons.chevron_right, color: AppColors.textHint, size: 20),
          ],
        ),
      ),
    );
  }

  void _confirmLogout(BuildContext context, AuthProvider auth) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              await auth.logout();
              if (context.mounted) {
                Navigator.pushNamedAndRemoveUntil(context, AppRoutes.login, (route) => false);
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }
}
