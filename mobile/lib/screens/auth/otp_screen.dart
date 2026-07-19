import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../routes/app_routes.dart';
import '../../utils/toast_helper.dart';
import '../../widgets/auth_widgets.dart';

class OtpScreen extends StatefulWidget {
  final String? phone;

  const OtpScreen({super.key, this.phone});

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> with TickerProviderStateMixin {
  final List<TextEditingController> _controllers = List.generate(
    4,
    (_) => TextEditingController(),
  );
  final List<FocusNode> _focusNodes = List.generate(4, (_) => FocusNode());
  bool _isVerifying = false;
  int _resendSeconds = 60;
  late AnimationController _fadeController;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      duration: const Duration(milliseconds: 400),
      vsync: this,
    );
    _fadeAnimation = CurvedAnimation(parent: _fadeController, curve: Curves.easeOut);
    _fadeController.forward();
    _startResendTimer();
  }

  void _startResendTimer() {
    setState(() => _resendSeconds = 60);
    Future.delayed(const Duration(seconds: 1), () {
      if (_resendSeconds > 0 && mounted) {
        setState(() => _resendSeconds--);
        _startResendTimer();
      }
    });
  }

  @override
  void dispose() {
    for (final c in _controllers) {
      c.dispose();
    }
    for (final f in _focusNodes) {
      f.dispose();
    }
    _fadeController.dispose();
    super.dispose();
  }

  String get _otp => _controllers.map((c) => c.text).join();

  Future<void> _handleVerify() async {
    if (_otp.length != 4) {
      ToastHelper.warning(context, 'Please enter the 4-digit code');
      return;
    }

    setState(() => _isVerifying = true);

    try {
      final auth = context.read<AuthProvider>();
      final res = await auth.verifyOtp(widget.phone ?? '', _otp);

      if (!mounted) return;

      if (res) {
        ToastHelper.success(context, 'Phone verified successfully!');
        Navigator.pushReplacementNamed(context, AppRoutes.dashboard);
      } else {
        ToastHelper.error(context, 'Invalid verification code');
      }
    } catch (e) {
      if (mounted) ToastHelper.error(context, 'Verification failed. Try again.');
    } finally {
      if (mounted) setState(() => _isVerifying = false);
    }
  }

  Future<void> _handleResend() async {
    if (_resendSeconds > 0) return;
    _startResendTimer();
    ToastHelper.info(context, 'OTP resent to your phone');
  }

  @override
  Widget build(BuildContext context) {
    return AuthBackground(
      child: FadeTransition(
        opacity: _fadeAnimation,
        child: AuthCard(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const AuthHeader(
                title: 'Verify Your Phone',
                subtitle: 'Enter the code sent to your number',
                tagline: 'Wazabiashara',
              ),
              Padding(
                padding: const EdgeInsets.all(32),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // OTP Icon
                    Container(
                      width: 64,
                      height: 64,
                      margin: const EdgeInsets.only(bottom: 24),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        color: AppColors.primary.withValues(alpha: 0.08),
                      ),
                      child: const Icon(
                        Icons.message_outlined,
                        size: 32,
                        color: AppColors.primary,
                      ),
                    ),
                    // OTP Input boxes
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      children: List.generate(4, (i) {
                        return SizedBox(
                          width: 56,
                          height: 64,
                          child: TextFormField(
                            controller: _controllers[i],
                            focusNode: _focusNodes[i],
                            keyboardType: TextInputType.number,
                            textAlign: TextAlign.center,
                            maxLength: 1,
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.w800,
                              color: Color(0xFF1F2937),
                            ),
                            decoration: InputDecoration(
                              counterText: '',
                              filled: true,
                              fillColor: Colors.white,
                              contentPadding: EdgeInsets.zero,
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: BorderSide(
                                  color: i == 0 ? AppColors.primary : const Color(0xFFE5E7EB),
                                  width: i == 0 ? 2 : 1,
                                ),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppColors.primary, width: 2),
                              ),
                              errorBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: Color(0xFFEF4444)),
                              ),
                            ),
                            onChanged: (value) {
                              if (value.isNotEmpty && i < 3) {
                                _focusNodes[i + 1].requestFocus();
                              }
                              if (value.isEmpty && i > 0) {
                                _focusNodes[i - 1].requestFocus();
                              }
                            },
                          ),
                        );
                      }),
                    ),
                    const SizedBox(height: 28),
                    // Verify button
                    Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(10),
                        gradient: const LinearGradient(
                          colors: [AppColors.primary, AppColors.primaryDark],
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.primary.withValues(alpha: 0.3),
                            blurRadius: 12,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Material(
                        color: Colors.transparent,
                        child: InkWell(
                          onTap: _isVerifying ? null : _handleVerify,
                          borderRadius: BorderRadius.circular(10),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                if (_isVerifying)
                                  const SizedBox(
                                    width: 18,
                                    height: 18,
                                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                  )
                                else
                                  const Icon(Icons.check_circle_outline, size: 20, color: Colors.white),
                                const SizedBox(width: 8),
                                Text(
                                  _isVerifying ? 'Verifying...' : 'Verify',
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                    color: Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    // Resend
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text(
                          "Didn't receive the code? ",
                          style: TextStyle(fontSize: 13, color: Color(0xFF6B7280)),
                        ),
                        GestureDetector(
                          onTap: _resendSeconds > 0 ? null : _handleResend,
                          child: Text(
                            _resendSeconds > 0
                                ? 'Resend in ${_resendSeconds}s'
                                : 'Resend OTP',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              color: _resendSeconds > 0
                                  ? const Color(0xFF9CA3AF)
                                  : AppColors.primary,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
