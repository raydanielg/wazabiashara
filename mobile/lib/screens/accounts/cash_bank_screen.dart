import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/account.dart';
import '../../services/api_service.dart';
import '../../utils/format_utils.dart';
import '../../widgets/loading_widget.dart';

class CashBankScreen extends StatefulWidget {
  const CashBankScreen({super.key});

  @override
  State<CashBankScreen> createState() => _CashBankScreenState();
}

class _CashBankScreenState extends State<CashBankScreen> {
  final _api = ApiService();
  List<Account> _accounts = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    try {
      final res = await _api.getAccounts();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() => _accounts = list.map((e) => Account.fromJson(e)).toList());
      } else {
        setState(() => _accounts = [Account(id: -1, name: 'Cash', type: 'cash')]);
      }
    } catch (_) {
      setState(() => _accounts = [Account(id: -1, name: 'Cash', type: 'cash')]);
    }
    setState(() => _isLoading = false);
  }

  double get _overallBalance => _accounts.fold(0, (sum, a) => sum + a.currentBalance);

  IconData _iconFor(String type) {
    switch (type) {
      case 'bank':
        return Icons.account_balance_outlined;
      case 'mobile_money':
        return Icons.phone_iphone_outlined;
      default:
        return Icons.payments_outlined;
    }
  }

  Future<void> _addAccount() async {
    final nameCtrl = TextEditingController();
    String type = 'bank';

    final result = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('New Account', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                const SizedBox(height: 20),
                TextField(controller: nameCtrl, autofocus: true, decoration: const InputDecoration(labelText: 'Account Name')),
                const SizedBox(height: 16),
                Wrap(
                  spacing: 8,
                  children: [
                    ChoiceChip(label: const Text('Bank'), selected: type == 'bank', onSelected: (_) => setSheetState(() => type = 'bank')),
                    ChoiceChip(label: const Text('Mobile Money'), selected: type == 'mobile_money', onSelected: (_) => setSheetState(() => type = 'mobile_money')),
                  ],
                ),
                const SizedBox(height: 24),
                ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Add Account')),
              ],
            ),
          ),
        ),
      ),
    );

    if (result != true || nameCtrl.text.trim().isEmpty) return;
    final name = nameCtrl.text.trim();

    try {
      await _api.createAccount({'name': name, 'type': type});
    } catch (_) {}
    setState(() => _accounts.add(Account(id: DateTime.now().millisecondsSinceEpoch, name: name, type: type)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Cash & Bank Accounts')),
      body: _isLoading
          ? const LoadingWidget()
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: AppColors.divider),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Overall Account Balance', style: TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      Text(FormatUtils.currency(_overallBalance), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800)),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('All Accounts', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                    TextButton.icon(
                      onPressed: _addAccount,
                      icon: const Icon(Icons.add, size: 18),
                      label: const Text('New Account'),
                      style: TextButton.styleFrom(foregroundColor: AppColors.primary, padding: EdgeInsets.zero),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Container(
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: AppColors.divider),
                  ),
                  child: Column(
                    children: _accounts.asMap().entries.map((entry) {
                      final a = entry.value;
                      final isLast = entry.key == _accounts.length - 1;
                      return Column(
                        children: [
                          ListTile(
                            leading: Container(
                              width: 36,
                              height: 36,
                              decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
                              child: Icon(_iconFor(a.type), color: AppColors.primary, size: 18),
                            ),
                            title: Text(a.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                            trailing: Text(FormatUtils.currency(a.currentBalance), style: const TextStyle(fontWeight: FontWeight.w700)),
                          ),
                          if (!isLast) const Divider(height: 1, indent: 56),
                        ],
                      );
                    }).toList(),
                  ),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () {},
                  style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                  child: const Text('Adjust Balance'),
                ),
              ],
            ),
    );
  }
}
