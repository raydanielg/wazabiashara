import 'package:flutter/material.dart';
import 'package:local_auth/local_auth.dart';
import '../../theme/app_theme.dart';
import '../../services/storage_service.dart';

/// Shown at app launch (after splash) when the user has turned on App Lock
/// in Security & PIN settings. Requires the saved PIN — or biometrics, if
/// also enabled — before replacing itself with [targetRoute].
///
/// Navigation happens from this screen's own BuildContext (not the caller's)
/// because by the time the PIN/biometric check succeeds, the screen that
/// pushed this one has usually already been disposed.
class PinLockScreen extends StatefulWidget {
  final String targetRoute;
  const PinLockScreen({super.key, required this.targetRoute});

  @override
  State<PinLockScreen> createState() => _PinLockScreenState();
}

class _PinLockScreenState extends State<PinLockScreen> {
  final _storage = StorageService();
  final _auth = LocalAuthentication();
  String _entered = '';
  String? _error;
  bool _biometricAvailable = false;

  @override
  void initState() {
    super.initState();
    _checkBiometric();
  }

  Future<void> _checkBiometric() async {
    final enabled = await _storage.getAppLockBiometric();
    if (!enabled) return;
    try {
      final canCheck = await _auth.canCheckBiometrics;
      final isSupported = await _auth.isDeviceSupported();
      if (mounted) setState(() => _biometricAvailable = canCheck && isSupported);
      if (_biometricAvailable) _tryBiometric();
    } catch (_) {}
  }

  Future<void> _tryBiometric() async {
    try {
      final ok = await _auth.authenticate(
        localizedReason: 'Unlock Wazabiashara',
        options: const AuthenticationOptions(biometricOnly: true, stickyAuth: true),
      );
      if (ok) _unlock();
    } catch (_) {}
  }

  void _unlock() {
    if (!mounted) return;
    Navigator.of(context).pushReplacementNamed(widget.targetRoute);
  }

  Future<void> _tap(String digit) async {
    if (_entered.length >= 6) return;
    setState(() {
      _entered += digit;
      _error = null;
    });
    if (_entered.length >= 4) {
      final saved = await _storage.getAppLockPin();
      if (_entered == saved) {
        _unlock();
      } else if (_entered.length == 6 || (saved != null && _entered.length == saved.length)) {
        setState(() {
          _error = 'Incorrect PIN';
          _entered = '';
        });
      }
    }
  }

  void _backspace() {
    if (_entered.isEmpty) return;
    setState(() => _entered = _entered.substring(0, _entered.length - 1));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.primary,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const SizedBox(height: 40),
              Container(
                width: 64,
                height: 64,
                decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.12), shape: BoxShape.circle),
                child: const Icon(Icons.lock_outline, color: Colors.white, size: 30),
              ),
              const SizedBox(height: 20),
              const Text('Enter your PIN', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
              const SizedBox(height: 8),
              Text(
                _error ?? 'Unlock to continue to Wazabiashara',
                style: TextStyle(color: _error != null ? AppColors.errorLight : Colors.white.withValues(alpha: 0.7), fontSize: 13),
              ),
              const SizedBox(height: 28),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(6, (i) {
                  final filled = i < _entered.length;
                  return Container(
                    width: 14,
                    height: 14,
                    margin: const EdgeInsets.symmetric(horizontal: 6),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: filled ? AppColors.gold : Colors.white.withValues(alpha: 0.2),
                    ),
                  );
                }),
              ),
              const Spacer(),
              _keypad(),
              const SizedBox(height: 12),
              if (_biometricAvailable)
                TextButton.icon(
                  onPressed: _tryBiometric,
                  icon: const Icon(Icons.fingerprint, color: Colors.white),
                  label: const Text('Use biometric unlock', style: TextStyle(color: Colors.white)),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _keypad() {
    final rows = [
      ['1', '2', '3'],
      ['4', '5', '6'],
      ['7', '8', '9'],
      ['', '0', 'back'],
    ];
    return Column(
      children: rows.map((row) {
        return Padding(
          padding: const EdgeInsets.symmetric(vertical: 6),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: row.map((key) {
              if (key.isEmpty) return const SizedBox(width: 64, height: 64);
              if (key == 'back') {
                return _padButton(child: const Icon(Icons.backspace_outlined, color: Colors.white, size: 22), onTap: _backspace);
              }
              return _padButton(child: Text(key, style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w700)), onTap: () => _tap(key));
            }).toList(),
          ),
        );
      }).toList(),
    );
  }

  Widget _padButton({required Widget child, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      customBorder: const CircleBorder(),
      child: SizedBox(width: 64, height: 64, child: Center(child: child)),
    );
  }
}
