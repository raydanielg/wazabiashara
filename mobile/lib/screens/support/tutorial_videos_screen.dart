import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

/// Matches the reference "Tutorial Videos" screen.
class TutorialVideosScreen extends StatefulWidget {
  const TutorialVideosScreen({super.key});

  @override
  State<TutorialVideosScreen> createState() => _TutorialVideosScreenState();
}

class _TutorialVideosScreenState extends State<TutorialVideosScreen> {
  String _filter = 'All Videos';

  static const _videos = [
    'How to use the app?',
    'How to download the app?',
    'How to create an account?',
    'How to create parties?',
    'How to use Quick POS to record sales?',
    'How to use Quick Entry to record transactions?',
    'How to add inventory?',
    'How to send payment reminders to customers?',
    'How to create & share a business card?',
    'How to contact customer service?',
    'How to record sales?',
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Tutorial Videos')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
            child: Row(
              children: [
                _chip('All Videos'),
                const SizedBox(width: 8),
                _chip('General'),
              ],
            ),
          ),
          Expanded(
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: _videos.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (ctx, i) => Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.divider)),
                child: Row(
                  children: [
                    Expanded(child: Text(_videos[i], style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13))),
                    const SizedBox(width: 8),
                    Container(
                      width: 32,
                      height: 32,
                      decoration: const BoxDecoration(color: AppColors.primary, shape: BoxShape.circle),
                      child: const Icon(Icons.play_arrow, color: Colors.white, size: 18),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _chip(String label) {
    final selected = _filter == label;
    return GestureDetector(
      onTap: () => setState(() => _filter = label),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : AppColors.surface,
          border: Border.all(color: selected ? AppColors.primary : AppColors.divider),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Text(label, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: selected ? Colors.white : AppColors.textSecondary)),
      ),
    );
  }
}
