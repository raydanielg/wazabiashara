import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/theme_provider.dart';
import '../../providers/auth_provider.dart';
import '../../routes/app_routes.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('Settings')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _profileCard(auth.user?.name ?? 'User', auth.user?.email ?? ''),
            const SizedBox(height: 24),
            _sectionTitle('Preferences'),
            const SizedBox(height: 12),
            _settingsCard([
              _toggleItem(
                icon: Icons.dark_mode_outlined,
                iconColor: AppColors.primary,
                title: 'Dark Mode',
                subtitle: 'Switch between light and dark theme',
                value: theme.isDarkMode,
                onChanged: (_) => theme.toggleDarkMode(),
              ),
              _divider(),
              _navItem(
                icon: Icons.language_outlined,
                iconColor: AppColors.info,
                title: 'Language',
                subtitle: theme.language == 'sw' ? 'Kiswahili' : 'English',
                onTap: () => _showLanguageSheet(context, theme),
              ),
              _divider(),
              _navItem(
                icon: Icons.notifications_outlined,
                iconColor: AppColors.warning,
                title: 'Notifications',
                subtitle: 'Manage your notification preferences',
                onTap: () {},
              ),
            ]),
            const SizedBox(height: 24),
            _sectionTitle('Business'),
            const SizedBox(height: 12),
            _settingsCard([
              _navItem(icon: Icons.store_outlined, iconColor: AppColors.primary, title: 'Business Profile', subtitle: 'Edit business details', onTap: () {}),
              _divider(),
              _navItem(icon: Icons.receipt_long_outlined, iconColor: AppColors.info, title: 'Receipt Settings', subtitle: 'Customize your receipts', onTap: () {}),
              _divider(),
              _navItem(icon: Icons.fingerprint_outlined, iconColor: AppColors.success, title: 'Security & PIN', subtitle: 'App lock and security', onTap: () {}),
            ]),
            const SizedBox(height: 24),
            _sectionTitle('Support'),
            const SizedBox(height: 12),
            _settingsCard([
              _navItem(icon: Icons.help_outline, iconColor: AppColors.info, title: 'Help & FAQ', subtitle: 'Get help using the app', onTap: () {}),
              _divider(),
              _navItem(icon: Icons.info_outline, iconColor: AppColors.primary, title: 'About', subtitle: 'Version 1.0.0', onTap: () {}),
            ]),
            const SizedBox(height: 24),
            _settingsCard([
              _navItem(
                icon: Icons.logout,
                iconColor: AppColors.error,
                title: 'Logout',
                subtitle: 'Sign out of your account',
                onTap: () => _confirmLogout(context, auth),
                isDestructive: true,
              ),
            ]),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _profileCard(String name, String email) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: AppColors.primaryGradient,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: AppColors.primary.withValues(alpha: 0.3), blurRadius: 16, offset: const Offset(0, 6)),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: Colors.white.withValues(alpha: 0.2),
            child: Text(name[0].toUpperCase(), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: Colors.white)),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.white)),
                if (email.isNotEmpty)
                  Text(email, style: TextStyle(fontSize: 13, color: Colors.white.withValues(alpha: 0.8))),
              ],
            ),
          ),
          Icon(Icons.edit, color: Colors.white.withValues(alpha: 0.7), size: 20),
        ],
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4),
      child: Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSecondary, letterSpacing: 0.5)),
    );
  }

  Widget _settingsCard(List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.divider),
      ),
      child: Column(children: children),
    );
  }

  Widget _divider() => const Divider(height: 1, indent: 56);

  Widget _toggleItem({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    required bool value,
    required ValueChanged<bool> onChanged,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(color: iconColor.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: iconColor, size: 20),
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
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }

  Widget _navItem({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(
                color: isDestructive ? AppColors.error.withValues(alpha: 0.1) : iconColor.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: isDestructive ? AppColors.error : iconColor, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: isDestructive ? AppColors.error : AppColors.textPrimary)),
                  Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                ],
              ),
            ),
            if (!isDestructive) const Icon(Icons.chevron_right, color: AppColors.textHint, size: 22),
          ],
        ),
      ),
    );
  }

  void _showLanguageSheet(BuildContext context, ThemeProvider theme) {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Select Language', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
            const SizedBox(height: 20),
            _langOption(ctx, theme, 'sw', 'Kiswahili'),
            _langOption(ctx, theme, 'en', 'English'),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  Widget _langOption(BuildContext ctx, ThemeProvider theme, String code, String name) {
    final selected = theme.language == code;
    return ListTile(
      leading: Icon(selected ? Icons.radio_button_checked : Icons.radio_button_off, color: selected ? AppColors.primary : AppColors.textHint),
      title: Text(name, style: TextStyle(fontWeight: selected ? FontWeight.w700 : FontWeight.w500)),
      onTap: () {
        theme.setLanguage(code);
        Navigator.pop(ctx);
      },
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
