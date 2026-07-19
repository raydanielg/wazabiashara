import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';

Future<Customer?> showSelectPartySheet(BuildContext context) {
  return showModalBottomSheet<Customer?>(
    context: context,
    isScrollControlled: true,
    shape: const RoundedRectangleBorder(
      borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
    ),
    builder: (context) => const _SelectPartySheet(),
  );
}

class _SelectPartySheet extends StatefulWidget {
  const _SelectPartySheet();

  @override
  State<_SelectPartySheet> createState() => _SelectPartySheetState();
}

class _SelectPartySheetState extends State<_SelectPartySheet> {
  final _searchCtrl = TextEditingController();
  List<Customer> _filtered = [];

  final List<Customer> _customers = [
    Customer(id: 0, name: 'Cash Sale'),
    Customer(id: 1, name: 'John Mwakyusa', phone: '0712 345 678'),
    Customer(id: 2, name: 'Asha Hassan', phone: '0789 654 321'),
    Customer(id: 3, name: 'Biashara Store', phone: '0755 111 222'),
    Customer(id: 4, name: 'Fatuma Ali', phone: '0766 333 444'),
    Customer(id: 5, name: 'Salim Juma', phone: '0733 555 666'),
  ];

  @override
  void initState() {
    super.initState();
    _filtered = _customers;
    _searchCtrl.addListener(_onSearch);
  }

  void _onSearch() {
    final q = _searchCtrl.text.toLowerCase();
    setState(() {
      _filtered = q.isEmpty
          ? _customers
          : _customers.where((c) => c.name.toLowerCase().contains(q)).toList();
    });
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return DraggableScrollableSheet(
      initialChildSize: 0.7,
      minChildSize: 0.4,
      maxChildSize: 0.9,
      expand: false,
      builder: (context, scrollController) {
        return Column(
          children: [
            Container(
              width: 40,
              height: 4,
              margin: const EdgeInsets.only(top: 12),
              decoration: BoxDecoration(
                color: AppColors.divider,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Select Party',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close, size: 22),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: TextField(
                controller: _searchCtrl,
                decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.search, size: 20),
                  hintText: 'Search customer...',
                ),
              ),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: ListView.builder(
                controller: scrollController,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                itemCount: _filtered.length,
                itemBuilder: (context, i) {
                  final c = _filtered[i];
                  final isCash = c.id == 0;
                  return ListTile(
                    leading: CircleAvatar(
                      radius: 20,
                      backgroundColor: isCash
                          ? AppColors.success.withValues(alpha: 0.1)
                          : AppColors.primary.withValues(alpha: 0.1),
                      child: Icon(
                        isCash ? Icons.payments_outlined : Icons.person,
                        color: isCash ? AppColors.success : AppColors.primary,
                        size: 20,
                      ),
                    ),
                    title: Text(
                      c.name,
                      style: const TextStyle(fontWeight: FontWeight.w600),
                    ),
                    subtitle: c.phone != null
                        ? Text(c.phone!, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary))
                        : null,
                    trailing: const Icon(Icons.chevron_right, color: AppColors.textHint),
                    onTap: () => Navigator.pop(context, c),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }
}
