import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../utils/format_utils.dart';
import '../../widgets/empty_state.dart';

class CustomersScreen extends StatefulWidget {
  const CustomersScreen({super.key});

  @override
  State<CustomersScreen> createState() => _CustomersScreenState();
}

class _CustomersScreenState extends State<CustomersScreen> {
  final _searchCtrl = TextEditingController();

  final List<Customer> _customers = [
    Customer(id: 1, name: 'John Doe', phone: '+255712345678', email: 'john@mail.com', currentDebt: 15000, status: 'active'),
    Customer(id: 2, name: 'Mama Asha', phone: '+255723456789', currentDebt: 0, status: 'active'),
    Customer(id: 3, name: 'Juma M.', phone: '+255734567890', currentDebt: 45000, creditLimit: 100000, status: 'active'),
    Customer(id: 4, name: 'Bi. Salama', phone: '+255745678901', email: 'salama@mail.com', currentDebt: 0, status: 'active'),
    Customer(id: 5, name: 'Hassan A.', phone: '+255756789012', currentDebt: 8000, status: 'active'),
    Customer(id: 6, name: 'Neema J.', phone: '+255767890123', currentDebt: 0, status: 'inactive'),
    Customer(id: 7, name: 'Fatma K.', phone: '+255778901234', currentDebt: 32000, creditLimit: 50000, status: 'active'),
    Customer(id: 8, name: 'Ali Z.', phone: '+255789012345', currentDebt: 0, status: 'active'),
  ];

  List<Customer> get _filtered {
    final q = _searchCtrl.text.toLowerCase();
    if (q.isEmpty) return _customers;
    return _customers.where((c) =>
      c.name.toLowerCase().contains(q) ||
      (c.phone?.toLowerCase().contains(q) ?? false)
    ).toList();
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
        title: const Text('Customers'),
        actions: [
          IconButton(onPressed: () => _showAddSheet(context), icon: const Icon(Icons.person_add_outlined)),
        ],
      ),
      body: Column(
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
                TextField(
                  controller: _searchCtrl,
                  onChanged: (_) => setState(() {}),
                  decoration: InputDecoration(
                    hintText: 'Search customers...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _searchCtrl.text.isNotEmpty
                        ? IconButton(onPressed: () { _searchCtrl.clear(); setState(() {}); }, icon: const Icon(Icons.clear))
                        : null,
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _filtered.isEmpty
                ? const EmptyState(icon: Icons.people_outline, title: 'No customers found')
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

  void _showAddSheet(BuildContext context) {
    final nameCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
        child: Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Add Customer', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 20),
              TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Customer Name')),
              const SizedBox(height: 12),
              TextField(controller: phoneCtrl, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone Number')),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Customer added'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
                  );
                },
                child: const Text('Add Customer'),
              ),
            ],
          ),
        ),
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
