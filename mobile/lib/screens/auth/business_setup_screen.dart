import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:dio/dio.dart';
import '../../theme/app_theme.dart';
import '../../models/business_type.dart';
import '../../providers/auth_provider.dart';
import '../../routes/app_routes.dart';
import '../../services/api_service.dart';
import '../../utils/toast_helper.dart';
import '../../widgets/auth_widgets.dart';

/// Shown right after Register (or first Login) when the signed-in user has
/// no business_id yet — mirrors the web app's separate "/business/register"
/// step. Without this, nothing else in the app has anywhere to store real
/// data: every list stays empty and every create silently fails.
class BusinessSetupScreen extends StatefulWidget {
  const BusinessSetupScreen({super.key});

  @override
  State<BusinessSetupScreen> createState() => _BusinessSetupScreenState();
}

class _BusinessSetupScreenState extends State<BusinessSetupScreen> {
  final _api = ApiService();
  final _formKey = GlobalKey<FormState>();

  final _businessNameCtrl = TextEditingController();
  final _businessPhoneCtrl = TextEditingController();
  final _businessEmailCtrl = TextEditingController();
  final _branchLocationCtrl = TextEditingController();
  final _branchNameCtrl = TextEditingController(text: 'Main Branch');

  static const _regions = [
    'Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma', 'Mbeya', 'Morogoro',
    'Tanga', 'Kilimanjaro', 'Zanzibar', 'Iringa', 'Tabora', 'Kigoma',
    'Shinyanga', 'Singida', 'Mtwara', 'Ruvuma', 'Lindi', 'Rukwa', 'Other',
  ];

  List<BusinessType> _types = [];
  BusinessType? _selectedType;
  String? _selectedRegion;
  bool _isLoadingTypes = true;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _loadTypes();
  }

  @override
  void dispose() {
    _businessNameCtrl.dispose();
    _businessPhoneCtrl.dispose();
    _businessEmailCtrl.dispose();
    _branchLocationCtrl.dispose();
    _branchNameCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadTypes() async {
    try {
      final res = await _api.getBusinessTypes();
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = (res.data['data'] as List?) ?? [];
        setState(() {
          _types = list.map((e) => BusinessType.fromJson(e)).toList();
          if (_types.isNotEmpty) _selectedType = _types.first;
        });
      }
    } catch (_) {
      // No connectivity yet — the picker just shows an empty/manual state;
      // the user can still submit once type validation is optional server-side.
    }
    setState(() => _isLoadingTypes = false);
  }

  Future<void> _pickType() async {
    if (_types.isEmpty) return;
    final selected = await showModalBottomSheet<BusinessType>(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Padding(
              padding: EdgeInsets.all(16),
              child: Text('Select Business Type', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            ),
            Flexible(
              child: ListView(
                shrinkWrap: true,
                children: _types
                    .map((t) => ListTile(
                          leading: const Icon(Icons.storefront_outlined, color: AppColors.primary),
                          title: Text(t.name),
                          onTap: () => Navigator.pop(ctx, t),
                        ))
                    .toList(),
              ),
            ),
          ],
        ),
      ),
    );
    if (selected != null) setState(() => _selectedType = selected);
  }

  Future<void> _pickRegion() async {
    final selected = await showModalBottomSheet<String>(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Padding(
              padding: EdgeInsets.all(16),
              child: Text('Select Region', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            ),
            Flexible(
              child: ListView(
                shrinkWrap: true,
                children: _regions.map((r) => ListTile(title: Text(r), onTap: () => Navigator.pop(ctx, r))).toList(),
              ),
            ),
          ],
        ),
      ),
    );
    if (selected != null) setState(() => _selectedRegion = selected);
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedRegion == null) {
      ToastHelper.error(context, 'Please select your region');
      return;
    }
    if (_types.isNotEmpty && _selectedType == null) {
      ToastHelper.error(context, 'Please select your business type');
      return;
    }

    setState(() => _isSaving = true);
    try {
      final res = await _api.registerBusiness({
        'business_name': _businessNameCtrl.text.trim(),
        'business_type': _selectedType?.slug,
        'region': _selectedRegion,
        'business_phone': _businessPhoneCtrl.text.trim(),
        'business_email': _businessEmailCtrl.text.trim().isEmpty ? null : _businessEmailCtrl.text.trim(),
        'branch_name': _branchNameCtrl.text.trim().isEmpty ? 'Main Branch' : _branchNameCtrl.text.trim(),
        'branch_location': _branchLocationCtrl.text.trim(),
      });

      if (!mounted) return;

      if (res.statusCode == 201 && res.data['success'] == true) {
        context.read<AuthProvider>().setUser(res.data['user'] as Map<String, dynamic>);
        ToastHelper.success(context, 'Business registered! Welcome to Wazabiashara.');
        Navigator.pushNamedAndRemoveUntil(context, AppRoutes.dashboard, (route) => false);
      } else {
        ToastHelper.error(context, res.data['message'] as String? ?? 'Could not register your business');
      }
    } on DioException catch (e) {
      final errors = e.response?.data['errors'] as Map<String, dynamic>?;
      final msg = errors != null && errors.isNotEmpty
          ? (errors.values.first as List).first as String
          : (e.response?.data['message'] as String? ?? 'Network error. Try again.');
      if (mounted) ToastHelper.error(context, msg);
    } catch (_) {
      if (mounted) ToastHelper.error(context, 'Something went wrong. Try again.');
    }

    if (mounted) setState(() => _isSaving = false);
  }

  @override
  Widget build(BuildContext context) {
    return AuthBackground(
      child: AuthCard(
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const AuthHeader(
                title: 'Set Up Your Business',
                subtitle: 'One last step — this is where all your data will live',
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 0, 24, 40),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    AuthTextField(
                      label: 'Business Name',
                      hint: 'e.g. Neema General Store',
                      icon: Icons.storefront_outlined,
                      controller: _businessNameCtrl,
                      validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter your business name' : null,
                    ),
                    const SizedBox(height: 20),
                    _pickerField(
                      label: 'Business Type',
                      icon: Icons.category_outlined,
                      value: _isLoadingTypes ? 'Loading...' : (_selectedType?.name ?? 'Select type'),
                      onTap: _pickType,
                    ),
                    const SizedBox(height: 20),
                    _pickerField(
                      label: 'Region',
                      icon: Icons.map_outlined,
                      value: _selectedRegion ?? 'Select region',
                      onTap: _pickRegion,
                    ),
                    const SizedBox(height: 20),
                    AuthTextField(
                      label: 'Business Phone',
                      hint: '07XX XXX XXX',
                      icon: Icons.phone_outlined,
                      keyboardType: TextInputType.phone,
                      controller: _businessPhoneCtrl,
                      validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter a phone number' : null,
                    ),
                    const SizedBox(height: 20),
                    AuthTextField(
                      label: 'Business Email (optional)',
                      hint: 'business@example.com',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      controller: _businessEmailCtrl,
                    ),
                    const SizedBox(height: 28),
                    const Row(
                      children: [
                        Icon(Icons.location_city_outlined, size: 16, color: AppColors.textSecondary),
                        SizedBox(width: 8),
                        Text('Your First Branch', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppColors.textSecondary)),
                      ],
                    ),
                    const SizedBox(height: 16),
                    AuthTextField(
                      label: 'Branch Name',
                      hint: 'Main Branch',
                      icon: Icons.store_outlined,
                      controller: _branchNameCtrl,
                    ),
                    const SizedBox(height: 20),
                    AuthTextField(
                      label: 'Branch Location',
                      hint: 'e.g. Kariakoo, Dar es Salaam',
                      icon: Icons.pin_drop_outlined,
                      controller: _branchLocationCtrl,
                      validator: (v) => (v == null || v.trim().isEmpty) ? 'Please enter the branch location' : null,
                    ),
                    const SizedBox(height: 28),
                    _SubmitButton(isLoading: _isSaving, onPressed: _submit),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _pickerField({required String label, required IconData icon, required String value, required VoidCallback onTap}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF374151))),
        const SizedBox(height: 6),
        InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(10),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: const Color(0xFFE5E7EB)),
            ),
            child: Row(
              children: [
                Icon(icon, size: 20, color: const Color(0xFF9CA3AF)),
                const SizedBox(width: 10),
                Expanded(child: Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500))),
                const Icon(Icons.expand_more, size: 18, color: AppColors.textHint),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _SubmitButton extends StatelessWidget {
  final bool isLoading;
  final VoidCallback onPressed;
  const _SubmitButton({required this.isLoading, required this.onPressed});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(10),
        gradient: const LinearGradient(colors: [AppColors.goldGradientStart, AppColors.goldGradientEnd]),
        boxShadow: [BoxShadow(color: AppColors.gold.withValues(alpha: 0.3), blurRadius: 12, offset: const Offset(0, 4))],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isLoading ? null : onPressed,
          borderRadius: BorderRadius.circular(10),
          child: Container(
            padding: const EdgeInsets.symmetric(vertical: 14),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (isLoading)
                  const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                else
                  const Icon(Icons.check_circle_outline, size: 20, color: Colors.white),
                const SizedBox(width: 8),
                Text(isLoading ? 'Setting up...' : 'Start Using Wazabiashara', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Colors.white)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
