import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_widget.dart';

/// Business Profile — view/edit the signed-in business's core details.
/// Wired to the real GET/PUT /business/profile endpoints
/// (BusinessProfileController), replacing what used to be a "coming soon"
/// placeholder on the More menu.
class BusinessProfileScreen extends StatefulWidget {
  const BusinessProfileScreen({super.key});

  @override
  State<BusinessProfileScreen> createState() => _BusinessProfileScreenState();
}

class _BusinessProfileScreenState extends State<BusinessProfileScreen> {
  final _api = ApiService();
  final _nameCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _vatCtrl = TextEditingController();

  bool _isLoading = true;
  bool _isSaving = false;
  bool _loadFailed = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    _emailCtrl.dispose();
    _addressCtrl.dispose();
    _vatCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _isLoading = true;
      _loadFailed = false;
    });
    try {
      final res = await _api.getBusinessProfile();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final b = res.data['business'] as Map<String, dynamic>;
        _nameCtrl.text = b['name'] as String? ?? '';
        _phoneCtrl.text = b['phone'] as String? ?? '';
        _emailCtrl.text = b['email'] as String? ?? '';
        _addressCtrl.text = b['address'] as String? ?? '';
        _vatCtrl.text = (b['vat_rate'] as num?)?.toString() ?? '';
      } else {
        setState(() => _loadFailed = true);
      }
    } catch (_) {
      setState(() => _loadFailed = true);
    }
    setState(() => _isLoading = false);
  }

  Future<void> _save() async {
    if (_nameCtrl.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Business name is required'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
      );
      return;
    }
    setState(() => _isSaving = true);
    try {
      await _api.updateBusinessProfile({
        'name': _nameCtrl.text.trim(),
        'phone': _phoneCtrl.text.trim().isEmpty ? null : _phoneCtrl.text.trim(),
        'email': _emailCtrl.text.trim().isEmpty ? null : _emailCtrl.text.trim(),
        'address': _addressCtrl.text.trim().isEmpty ? null : _addressCtrl.text.trim(),
        'vat_rate': double.tryParse(_vatCtrl.text),
      });
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Business profile updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
      );
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not save — check your connection and try again'), backgroundColor: AppColors.error, behavior: SnackBarBehavior.floating),
        );
      }
    }
    if (mounted) setState(() => _isSaving = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Business Profile')),
      body: _isLoading
          ? const LoadingWidget(message: 'Loading business profile...')
          : _loadFailed
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.wifi_off_rounded, size: 48, color: AppColors.textHint),
                        const SizedBox(height: 16),
                        const Text('Could not load your business profile', style: TextStyle(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 16),
                        ElevatedButton(onPressed: _load, child: const Text('Retry')),
                      ],
                    ),
                  ),
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Business Name', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      TextField(controller: _nameCtrl, decoration: const InputDecoration(hintText: 'Business name')),
                      const SizedBox(height: 16),
                      const Text('Phone', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      TextField(controller: _phoneCtrl, keyboardType: TextInputType.phone, decoration: const InputDecoration(hintText: '07XX XXX XXX')),
                      const SizedBox(height: 16),
                      const Text('Email', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      TextField(controller: _emailCtrl, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(hintText: 'business@example.com')),
                      const SizedBox(height: 16),
                      const Text('Address', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      TextField(controller: _addressCtrl, maxLines: 2, decoration: const InputDecoration(hintText: 'Street, City')),
                      const SizedBox(height: 16),
                      const Text('VAT Rate (%)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.textSecondary)),
                      const SizedBox(height: 6),
                      TextField(controller: _vatCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(hintText: '0')),
                      const SizedBox(height: 28),
                      ElevatedButton(
                        onPressed: _isSaving ? null : _save,
                        style: ElevatedButton.styleFrom(minimumSize: const Size.fromHeight(50)),
                        child: _isSaving
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text('Save Changes'),
                      ),
                    ],
                  ),
                ),
    );
  }
}
