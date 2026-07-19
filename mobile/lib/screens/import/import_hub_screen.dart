import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'import_steps_screen.dart';

/// "Import Data" hub — a choice between importing Parties or Items, each
/// opening the matching 3-step import screen.
class ImportHubScreen extends StatelessWidget {
  const ImportHubScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Import Data')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            _card(
              context,
              icon: Icons.people_outline,
              title: 'Import Parties',
              subtitle: 'Bulk-add customers & suppliers from an Excel file',
              onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ImportStepsScreen(type: ImportType.parties))),
            ),
            const SizedBox(height: 12),
            _card(
              context,
              icon: Icons.inventory_2_outlined,
              title: 'Import Items',
              subtitle: 'Bulk-add products & stock from an Excel file',
              onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ImportStepsScreen(type: ImportType.items))),
            ),
          ],
        ),
      ),
    );
  }

  Widget _card(BuildContext context, {required IconData icon, required String title, required String subtitle, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.divider)),
        child: Row(
          children: [
            Container(width: 44, height: 44, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(12)), child: Icon(icon, color: AppColors.primary)),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15)),
                  const SizedBox(height: 2),
                  Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: AppColors.textHint),
          ],
        ),
      ),
    );
  }
}
