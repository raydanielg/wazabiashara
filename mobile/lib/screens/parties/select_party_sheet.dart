import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../services/api_service.dart';
import 'add_party_screen.dart';

/// The "Select Party for Sale" bottom sheet — search box, a "Cash Sale"
/// shortcut at the top, the party list, and an "Add New Party" action that
/// opens [AddPartyScreen] and immediately selects the result.
///
/// Returns the selected [Customer], or a synthetic `id: 0` "Cash Sale"
/// customer, or `null` if dismissed without a selection.
Future<Customer?> showSelectPartySheet(BuildContext context) {
  return showModalBottomSheet<Customer>(
    context: context,
    isScrollControlled: true,
    shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
    builder: (ctx) => const _SelectPartySheet(),
  );
}

class _SelectPartySheet extends StatefulWidget {
  const _SelectPartySheet();

  @override
  State<_SelectPartySheet> createState() => _SelectPartySheetState();
}

class _SelectPartySheetState extends State<_SelectPartySheet> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();
  List<Customer> _parties = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final res = await _api.getCustomers();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _parties = list.map((e) => Customer.fromJson(e)).toList());
      }
    } catch (_) {
      // Leave the list empty — the sheet still works for Cash Sale / adding
      // a brand-new party even without connectivity.
    }
    setState(() => _isLoading = false);
  }

  List<Customer> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    if (q.isEmpty) return _parties;
    return _parties.where((c) => c.name.toLowerCase().contains(q)).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: DraggableScrollableSheet(
        initialChildSize: 0.75,
        minChildSize: 0.5,
        maxChildSize: 0.92,
        expand: false,
        builder: (ctx, scrollController) {
          return Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.divider, borderRadius: BorderRadius.circular(2))),
                ),
                const SizedBox(height: 16),
                const Text('Select Party for Sale', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                const SizedBox(height: 16),
                TextField(
                  controller: _searchCtrl,
                  onChanged: (_) => setState(() {}),
                  decoration: const InputDecoration(hintText: 'Enter party name...', prefixIcon: Icon(Icons.search)),
                ),
                const SizedBox(height: 12),
                Expanded(
                  child: ListView(
                    controller: scrollController,
                    children: [
                      _partyTile(
                        avatar: const Icon(Icons.point_of_sale, color: Colors.white, size: 18),
                        avatarColor: AppColors.primary,
                        name: 'Cash Sale',
                        subtitle: 'Walk-in customer, no record kept',
                        onTap: () => Navigator.pop(context, Customer(id: 0, name: 'Cash Sale')),
                      ),
                      const Divider(height: 24),
                      if (_isLoading)
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 24),
                          child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                        )
                      else
                        ..._filtered.map((c) => _partyTile(
                              avatar: Text(c.name.isNotEmpty ? c.name[0].toUpperCase() : '?', style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary)),
                              avatarColor: AppColors.primary.withValues(alpha: 0.1),
                              name: c.name,
                              subtitle: c.phone ?? c.email ?? '',
                              badge: 'From Contacts',
                              onTap: () => Navigator.pop(context, c),
                            )),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                ElevatedButton.icon(
                  onPressed: () async {
                    final created = await Navigator.push<Customer>(
                      context,
                      MaterialPageRoute(builder: (_) => AddPartyScreen(initialName: _searchCtrl.text.trim())),
                    );
                    if (created != null && context.mounted) Navigator.pop(context, created);
                  },
                  icon: const Icon(Icons.person_add_alt_1),
                  label: const Text('Add New Party'),
                  style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(48)),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _partyTile({
    required Widget avatar,
    required Color avatarColor,
    required String name,
    required String subtitle,
    String? badge,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 10),
        child: Row(
          children: [
            CircleAvatar(radius: 20, backgroundColor: avatarColor, child: avatar),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  if (subtitle.isNotEmpty) Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                ],
              ),
            ),
            if (badge != null)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(color: AppColors.divider, borderRadius: BorderRadius.circular(8)),
                child: Text(badge, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: AppColors.textSecondary)),
              ),
          ],
        ),
      ),
    );
  }
}
