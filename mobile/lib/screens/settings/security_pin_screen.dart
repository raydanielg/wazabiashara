import 'package:flutter/material.dart';
import 'package:local_auth/local_auth.dart';
import '../../theme/app_theme.dart';
import '../../services/storage_service.dart';

/// Security & PIN — lets the user require a PIN (and optionally biometric
/// unlock) every time the app launches. Persisted for real via
/// StorageService/flutter_secure_storage, and enforced at launch by
/// PinLockScreen (wired from splash_screen.dart). Replaces the old
/// onTap: () {} stub.
class SecurityPinScreen extends StatefulWidget {
  const SecurityPinScreen({super.key});

  @override
  State<SecurityPinScreen> createState() => _SecurityPinScreenState();
}

class _SecurityPinScreenState extends State<SecurityPinScreen> {
  final _storage = StorageService();
  final _auth = LocalAuthentication();
  bool _isLoading = true;
  bool _lockEnabled = false;
  bool _biometricEnabled = false;
  bool _biometricAvailable = false;
  bool _hasPin = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final lockEnabled = await _storage.getAppLockEnabled();
    final biometricEnabled = await _storage.getAppLockBiometric();
    final pin = await _storage.getAppLockPin();
    bool biometricAvailable = false;
    try {
      biometricAvailable = await _auth.canCheckBiometrics && await _auth.isDeviceSupported();
    } catch (_) {}
    if (!mounted) return;
    setState(() {
      _lockEnabled = lockEnabled;
      _biometricEnabled = biometricEnabled;
      _hasPin = pin != null && pin.isNotEmpty;
      _biometricAvailable = biometricAvailable;
      _isLoading = false;
    });
  }

  Future<void> _toggleLock(bool value) async {
    if (value && !_hasPin) {
      final pin = await _promptSetPin();
      if (pin == null) return;
      await _storage.setAppLockPin(pin);
      setState(() => _hasPin = true);
    }
    await _storage.setAppLockEnabled(value);
    setState(() => _lockEnabled = value);
  }

  Future<void> _toggleBiometric(bool value) async {
    await _storage.setAppLockBiometric(value);
    setState(() => _biometricEnabled = value);
  }

  Future<String?> _promptSetPin() async {
    final ctrl1 = TextEditingController();
    final ctrl2 = TextEditingController();
    return showDialog<String>(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) {
          String? error;
          return AlertDialog(
            title: const Text('Set a PIN'),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: ctrl1,
                  keyboardType: TextInputType.number,
                  obscureText: true,
                  maxLength: 6,
                  decoration: const InputDecoration(labelText: 'New PIN (4-6 digits)', counterText: ''),
                ),
                const SizedBox(height: 8),
                TextField(
                  controller: ctrl2,
                  keyboardType: TextInputType.number,
                  obscureText: true,
                  maxLength: 6,
                  decoration: const InputDecoration(labelText: 'Confirm PIN', counterText: ''),
                ),
                if (error != null) Padding(padding: const EdgeInsets.only(top: 8), child: Text(error, style: const TextStyle(color: AppColors.error, fontSize: 12))),
              ],
            ),
            actions: [
              TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
              ElevatedButton(
                onPressed: () {
                  if (ctrl1.text.length < 4) {
                    setDialogState(() => error = 'PIN must be at least 4 digits');
                    return;
                  }
                  if (ctrl1.text != ctrl2.text) {
                    setDialogState(() => error = 'PINs do not match');
                    return;
                  }
                  Navigator.pop(ctx, ctrl1.text);
                },
                child: const Text('Save'),
              ),
            ],
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Security & PIN')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(strokeWidth: 2))
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Container(
                  decoration: BoxDecoration(color: context.cardBg, borderRadius: BorderRadius.circular(16), border: Border.all(color: context.borderColor)),
                  child: Column(
                    children: [
                      _toggleRow(
                        icon: Icons.lock_outline,
                        title: 'App Lock',
                        subtitle: 'Require a PIN every time you open the app',
                        value: _lockEnabled,
                        onChanged: _toggleLock,
                      ),
                      if (_lockEnabled) ...[
                        Divider(height: 1, indent: 56, color: context.borderColor),
                        ListTile(
                          leading: const Icon(Icons.pin_outlined, color: AppColors.primary),
                          title: const Text('Change PIN', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                          trailing: const Icon(Icons.chevron_right),
                          onTap: () async {
                            final pin = await _promptSetPin();
                            if (pin != null) {
                              await _storage.setAppLockPin(pin);
                              if (mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('PIN updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating),
                                );
                              }
                            }
                          },
                        ),
                        if (_biometricAvailable) ...[
                          Divider(height: 1, indent: 56, color: context.borderColor),
                          _toggleRow(
                            icon: Icons.fingerprint,
                            title: 'Biometric Unlock',
                            subtitle: 'Use fingerprint or face unlock instead of typing your PIN',
                            value: _biometricEnabled,
                            onChanged: _toggleBiometric,
                          ),
                        ],
                      ],
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4),
                  child: Text(
                    'Your PIN is stored securely on this device only — it never leaves your phone.',
                    style: TextStyle(fontSize: 12, color: context.textSecondaryColor, height: 1.4),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _toggleRow({required IconData icon, required String title, required String subtitle, required bool value, required ValueChanged<bool> onChanged}) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: AppColors.primary, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                Text(subtitle, style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
              ],
            ),
          ),
          Switch(value: value, onChanged: onChanged, activeColor: AppColors.primary),
        ],
      ),
    );
  }
}
