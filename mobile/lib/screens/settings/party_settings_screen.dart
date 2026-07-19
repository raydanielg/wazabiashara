import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Party Settings" screen.
class PartySettingsScreen extends StatefulWidget {
  const PartySettingsScreen({super.key});

  @override
  State<PartySettingsScreen> createState() => _PartySettingsScreenState();
}

class _PartySettingsScreenState extends State<PartySettingsScreen> {
  bool _partyCategory = false;
  bool _uploadImage = true;
  bool _shareTransactionLink = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Party Settings')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Container(
          decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.divider)),
          child: Column(
            children: [
              _toggle('Party Category', 'Enable Party Category to effortlessly manage Parties.', _partyCategory, (v) => setState(() => _partyCategory = v)),
              const Divider(height: 1),
              _toggle('Upload Image', 'Enable party image uploads to recognize parties easily.', _uploadImage, (v) => setState(() => _uploadImage = v)),
              const Divider(height: 1),
              _toggle('Share Party Transaction Link', 'You can share party transactions link to your parties while sending reminder from where they can view', _shareTransactionLink, (v) => setState(() => _shareTransactionLink = v)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _toggle(String title, String subtitle, bool value, ValueChanged<bool> onChanged) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                const SizedBox(height: 4),
                Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary, height: 1.35)),
              ],
            ),
          ),
          const SizedBox(width: 12),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }
}
