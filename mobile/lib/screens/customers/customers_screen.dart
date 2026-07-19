import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/loading_widget.dart';
import '../../services/api_service.dart';
import '../parties/add_party_screen.dart';

class CustomersScreen extends StatefulWidget {
  const CustomersScreen({super.key});

  @override
  State<CustomersScreen> createState() => _CustomersScreenState();
}

class _CustomersScreenState extends State<CustomersScreen> {
  final _api = ApiService();
  final _searchCtrl = TextEditingController();

  List<Customer> _customers = [];
  bool _isLoading = true;
  final Set<String> _partyTypes = {'Customer'};
  String _paymentFilter = 'All Payment';

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getCustomers();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _customers = list.map((e) => Customer.fromJson(e)).toList());
      }
    } catch (_) {
      // No connectivity yet — show the real empty state rather than fake
      // demo data, matching a genuinely fresh account.
    }
    setState(() => _isLoading = false);
  }

  Future<void> _addParty() async {
    final created = await Navigator.push<Customer>(context, MaterialPageRoute(builder: (_) => const AddPartyScreen()));
    if (created != null) setState(() => _customers.add(created));
  }

  List<Customer> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    // This screen currently sources "parties" from the Customers API only —
    // the Supplier chip is shown for parity with the reference design but
    // suppliers live in a separate list until the two are merged server-side.
    if (!_partyTypes.contains('Customer')) return [];

    return _customers.where((c) {
      final matchesSearch = q.isEmpty || c.name.toLowerCase().contains(q) || (c.phone?.toLowerCase().contains(q) ?? false);
      final matchesPayment = switch (_paymentFilter) {
        'To Receive' => c.currentDebt > 0,
        'Settled' => c.currentDebt == 0,
        'To Give' => false,
        _ => true,
      };
      return matchesSearch && matchesPayment;
    }).toList();
  }

  double get _totalDebt => _customers.fold(0, (sum, c) => sum + c.currentDebt);
  int get _debtors => _customers.where((c) => c.hasDebt).length;

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Parties'),
        actions: [
          IconButton(onPressed: _addParty, icon: const Icon(Icons.person_add_outlined)),
        ],
      ),
      body: _isLoading
          ? const LoadingWidget()
          : _customers.isEmpty
              ? EmptyState(
                  icon: Icons.people_outline,
                  title: "Let's Add Your First Party",
                  subtitle: 'Click on the add new party button and manage receivables & payables with them.',
                  actionLabel: 'Add New Party',
                  onAction: _addParty,
                )
              : Column(
                  children: [
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: _summaryCard('Total Debt', FormatUtils.currencyShort(_totalDebt), AppColors.error, Icons.warning_amber),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: _summaryCard('Debtors', '$_debtors', AppColors.warning, Icons.people_outline),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Expanded(
                                child: TextField(
                                  controller: _searchCtrl,
                                  onChanged: (_) => setState(() {}),
                                  decoration: InputDecoration(
                                    hintText: 'Search parties...',
                                    prefixIcon: const Icon(Icons.search),
                                    suffixIcon: _searchCtrl.text.isNotEmpty
                                        ? IconButton(onPressed: () { _searchCtrl.clear(); setState(() {}); }, icon: const Icon(Icons.clear))
                                        : null,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              _iconBtn(Icons.filter_list, _showPaymentFilterSheet),
                              const SizedBox(width: 8),
                              _iconBtn(Icons.filter_none, () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Export parties — coming soon'), behavior: SnackBarBehavior.floating),
                                );
                              }),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Row(
                            children: [
                              _typeChip('Customer'),
                              const SizedBox(width: 8),
                              _typeChip('Supplier'),
                              const SizedBox(width: 8),
                              _paymentChip(),
                            ],
                          ),
                        ],
                      ),
                    ),
                    Expanded(
                      child: _filtered.isEmpty
                          ? EmptyState(
                              icon: Icons.folder_open_outlined,
                              title: 'No Parties Data Found',
                              subtitle: 'Please search different party name or create new party.',
                            )
                          : ListView.builder(
                              padding: const EdgeInsets.symmetric(horizontal: 16),
                              itemCount: _filtered.length,
                              itemBuilder: (ctx, i) => _CustomerTile(
                                customer: _filtered[i],
                                onTap: () => _showDetail(context, _filtered[i]),
                              ),
                            ),
                    ),
                  ],
                ),
    );
  }

  Widget _iconBtn(IconData icon, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        width: 44,
        height: 44,
        decoration: BoxDecoration(border: Border.all(color: AppColors.divider), borderRadius: BorderRadius.circular(10)),
        child: Icon(icon, color: AppColors.textSecondary, size: 20),
      ),
    );
  }

  Widget _typeChip(String label) {
    final selected = _partyTypes.contains(label);
    return GestureDetector(
      onTap: () => setState(() {
        if (selected) {
          _partyTypes.remove(label);
        } else {
          _partyTypes.add(label);
        }
      }),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : AppColors.surface,
          border: Border.all(color: selected ? AppColors.primary : AppColors.divider),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(label, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: selected ? Colors.white : AppColors.textSecondary)),
            if (selected) ...[
              const SizedBox(width: 6),
              const Icon(Icons.close, size: 14, color: Colors.white),
            ],
          ],
        ),
      ),
    );
  }

  Widget _paymentChip() {
    final active = _paymentFilter != 'All Payment';
    return GestureDetector(
      onTap: _showPaymentFilterSheet,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: active ? AppColors.primary : AppColors.surface,
          border: Border.all(color: active ? AppColors.primary : AppColors.divider),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(_paymentFilter, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: active ? Colors.white : AppColors.textSecondary)),
            const SizedBox(width: 4),
            Icon(Icons.expand_more, size: 16, color: active ? Colors.white : AppColors.textSecondary),
          ],
        ),
      ),
    );
  }

  void _showPaymentFilterSheet() {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(20, 20, 20, 8),
            child: Text('Filter by Payment Type', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
          ),
          ...['All Payment', 'To Give', 'To Receive', 'Settled'].map((option) => RadioListTile<String>(
                value: option,
                groupValue: _paymentFilter,
                activeColor: AppColors.primary,
                title: Text(option, style: const TextStyle(fontWeight: FontWeight.w600)),
                onChanged: (v) {
                  setState(() => _paymentFilter = v ?? 'All Payment');
                  Navigator.pop(ctx);
                },
              )),
          const SizedBox(height: 12),
        ],
      ),
    );
  }

  Widget _summaryCard(String title, String value, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(value, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: color)),
                Text(title, style: const TextStyle(fontSize: 11, color: AppColors.textSecondary)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _showDetail(BuildContext context, Customer customer) {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundColor: AppColors.primary,
                  child: Text(customer.name[0], style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: Colors.white)),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(customer.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                      if (customer.phone != null) Text(customer.phone!, style: const TextStyle(color: AppColors.textSecondary)),
                    ],
                  ),
                ),
              ],
            ),
            const Divider(height: 32),
            if (customer.email != null) _row('Email', customer.email!),
            if (customer.address != null) _row('Address', customer.address!),
            _row('Current Debt', FormatUtils.currency(customer.currentDebt), color: customer.hasDebt ? AppColors.error : AppColors.success),
            if (customer.creditLimit != null) _row('Credit Limit', FormatUtils.currency(customer.creditLimit!)),
            _row('Status', customer.status.toUpperCase()),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(child: OutlinedButton.icon(onPressed: () => Navigator.pop(ctx), icon: const Icon(Icons.close), label: const Text('Close'))),
                if (customer.hasDebt) ...[
                  const SizedBox(width: 12),
                  Expanded(child: ElevatedButton.icon(onPressed: () { Navigator.pop(ctx); }, icon: const Icon(Icons.payments), label: const Text('Collect'))),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(String label, String value, {Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: AppColors.textSecondary)),
          Text(value, style: TextStyle(fontWeight: FontWeight.w700, color: color ?? AppColors.textPrimary)),
        ],
      ),
    );
  }

}

class _CustomerTile extends StatelessWidget {
  final Customer customer;
  final VoidCallback onTap;

  const _CustomerTile({required this.customer, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.divider),
        ),
        child: Row(
          children: [
            CircleAvatar(
              radius: 22,
              backgroundColor: AppColors.primary.withValues(alpha: 0.1),
              child: Text(customer.name[0], style: const TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary)),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(customer.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  if (customer.phone != null)
                    Text(customer.phone!, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                ],
              ),
            ),
            if (customer.hasDebt)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.error.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(FormatUtils.currencyShort(customer.currentDebt), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.error)),
              )
            else
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.success.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Text('Clear', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppColors.success)),
              ),
          ],
        ),
      ),
    );
  }
}
