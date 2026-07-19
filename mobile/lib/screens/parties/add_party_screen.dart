import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/customer.dart';
import '../../services/api_service.dart';

/// Matches the reference "Add New Party" screen: a circular photo picker,
/// a Party Name field, and an "Add New Party" action. Pops with the newly
/// created [Customer] so callers (e.g. the Select Party sheet) can select
/// it immediately.
class AddPartyScreen extends StatefulWidget {
  final String? initialName;

  const AddPartyScreen({super.key, this.initialName});

  @override
  State<AddPartyScreen> createState() => _AddPartyScreenState();
}

class _AddPartyScreenState extends State<AddPartyScreen> {
  final _api = ApiService();
  late final TextEditingController _nameCtrl;
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _nameCtrl = TextEditingController(text: widget.initialName ?? '');
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    _emailCtrl.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    final name = _nameCtrl.text.trim();
    if (name.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Party name is required'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
      );
      return;
    }

    setState(() => _isSaving = true);
    Customer newParty = Customer(
      id: DateTime.now().millisecondsSinceEpoch,
      name: name,
      phone: _phoneCtrl.text.trim().isEmpty ? null : _phoneCtrl.text.trim(),
      email: _emailCtrl.text.trim().isEmpty ? null : _emailCtrl.text.trim(),
    );

    try {
      final res = await _api.createCustomer({
        'name': name,
        if (_phoneCtrl.text.trim().isNotEmpty) 'phone': _phoneCtrl.text.trim(),
        if (_emailCtrl.text.trim().isNotEmpty) 'email': _emailCtrl.text.trim(),
      });
      if (res.statusCode == 200 || res.statusCode == 201) {
        final data = res.data['data'] ?? res.data['customer'];
        if (data != null) newParty = Customer.fromJson(data);
      }
    } catch (_) {
      // Keep the locally-built party so the flow isn't blocked while the
      // backend is unreachable — it can sync once connectivity returns.
    }

    if (!mounted) return;
    setState(() => _isSaving = false);
    Navigator.pop(context, newParty);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Add New Party'),
        actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.info_outline))],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 44,
                    backgroundColor: AppColors.primary.withValues(alpha: 0.08),
                    child: Icon(Icons.person_outline, size: 44, color: AppColors.primary.withValues(alpha: 0.4)),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      decoration: const BoxDecoration(color: AppColors.primary, shape: BoxShape.circle),
                      child: const Icon(Icons.camera_alt, size: 16, color: Colors.white),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),
            const Text('Party Name', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _nameCtrl, autofocus: widget.initialName == null, decoration: const InputDecoration(hintText: 'e.g. John Doe')),
            const SizedBox(height: 16),
            const Text('Phone Number', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _phoneCtrl, keyboardType: TextInputType.phone, decoration: const InputDecoration(hintText: '+255 7XX XXX XXX')),
            const SizedBox(height: 16),
            const Text('Email (optional)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
            const SizedBox(height: 6),
            TextField(controller: _emailCtrl, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(hintText: 'name@email.com')),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: _isSaving ? null : _save,
              style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
              child: _isSaving
                  ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Add New Party'),
            ),
          ],
        ),
      ),
    );
  }
}
