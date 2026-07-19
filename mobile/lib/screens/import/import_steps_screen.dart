import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../theme/app_theme.dart';

enum ImportType { parties, items }

/// Matches the reference "Import Parties/Items in 3 Steps" screen: a
/// numbered step list explaining the Excel import flow, a note on file
/// limits, and Select a File / Watch Video actions.
class ImportStepsScreen extends StatelessWidget {
  final ImportType type;
  const ImportStepsScreen({super.key, required this.type});

  String get _label => type == ImportType.parties ? 'Parties' : 'Items';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Import $_label in 3 Steps')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Expanded(
              child: ListView(
                children: [
                  _step(
                    number: 1,
                    title: 'Download file & Fill the Data',
                    body: 'Download Sample Excel File & fill the data according to the format.',
                    trailing: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 10),
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.divider)),
                          child: Text(
                            type == ImportType.parties
                                ? 'Party Name | Phone | Customer/Supplier | Opening Balance | Address'
                                : 'Item Name | Category | Purchase Price | Sales Price | Stock | Unit',
                            style: const TextStyle(fontSize: 10, color: AppColors.textSecondary),
                          ),
                        ),
                        const SizedBox(height: 10),
                        TextButton.icon(
                          onPressed: () => _showSampleFormat(context),
                          icon: const Icon(Icons.download_outlined, size: 16),
                          label: const Text('Get Sample File'),
                          style: TextButton.styleFrom(foregroundColor: AppColors.primary, padding: EdgeInsets.zero),
                        ),
                      ],
                    ),
                    isLast: false,
                  ),
                  _step(
                    number: 2,
                    title: 'Review & Adjust Data',
                    body: 'Review the data to be imported from the app itself. If there are any errors you can fix it from the app itself and make your data ready to import.',
                    isLast: false,
                  ),
                  _step(
                    number: 3,
                    title: 'Confirm & Import',
                    body: 'When everything is ready to import you can start the import process and your data will be imported shortly.',
                    isLast: true,
                  ),
                ],
              ),
            ),
            const Text(
              'Only excel file upto 500 entries & 1MB is supported.',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 11, color: AppColors.textHint),
            ),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('File upload is being finished — for now, add items or parties one at a time from their Add screens.'),
                    behavior: SnackBarBehavior.floating,
                  ),
                );
              },
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: const Text('Select a File'),
            ),
            const SizedBox(height: 10),
            OutlinedButton.icon(
              onPressed: () {},
              icon: const Icon(Icons.play_circle_outline),
              label: const Text('Watch Video'),
              style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
            ),
          ],
        ),
      ),
    );
  }

  void _showSampleFormat(BuildContext context) {
    final header = type == ImportType.parties
        ? 'Party Name,Phone,Type,Opening Balance,Address'
        : 'Item Name,Category,Purchase Price,Sales Price,Stock,Unit';
    final sampleRow = type == ImportType.parties
        ? 'John Doe,255712345678,Customer,0,Dar es Salaam'
        : 'Rice 1kg,Groceries,1800,2200,50,piece';
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Sample File Format'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Use these exact column headers in your spreadsheet:', style: TextStyle(fontSize: 13)),
            const SizedBox(height: 10),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.06), borderRadius: BorderRadius.circular(8)),
              child: SelectableText(header, style: const TextStyle(fontSize: 11, fontFamily: 'monospace')),
            ),
            const SizedBox(height: 8),
            const Text('Example row:', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
            const SizedBox(height: 4),
            SelectableText(sampleRow, style: const TextStyle(fontSize: 11, fontFamily: 'monospace')),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () {
              Clipboard.setData(ClipboardData(text: '$header\n$sampleRow'));
              ScaffoldMessenger.of(ctx).showSnackBar(
                const SnackBar(content: Text('Copied — paste into a new spreadsheet'), behavior: SnackBarBehavior.floating),
              );
            },
            child: const Text('Copy'),
          ),
          ElevatedButton(onPressed: () => Navigator.pop(ctx), child: const Text('Close')),
        ],
      ),
    );
  }

  Widget _step({required int number, required String title, required String body, Widget? trailing, required bool isLast}) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Column(
            children: [
              Container(
                width: 28,
                height: 28,
                alignment: Alignment.center,
                decoration: const BoxDecoration(color: AppColors.divider, shape: BoxShape.circle),
                child: Text('$number', style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.textPrimary)),
              ),
              if (!isLast) Expanded(child: Container(width: 2, color: AppColors.divider)),
            ],
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.only(bottom: 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15)),
                  const SizedBox(height: 4),
                  Text(body, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary, height: 1.4)),
                  if (trailing != null) trailing,
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
