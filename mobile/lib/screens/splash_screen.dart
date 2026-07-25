import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../routes/app_routes.dart';
import '../services/storage_service.dart';
import 'auth/pin_lock_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _gifFade;
  late Animation<double> _gifScale;
  late Animation<double> _nameFade;
  late Animation<double> _nameSlide;
  late Animation<double> _taglineFade;
  late Animation<double> _taglineSlide;
  late Animation<double> _fadeout;

  static const _word = 'Wazabiashara';
  static const _tagline = 'Biashara Yako, Mkononi Mwako';
  static const _totalMs = 4000;

  // Timeline (ms)
  static const _gifStart = 0.0;
  static const _gifDur = 2000.0;
  static const _nameStart = 2200.0;
  static const _nameDur = 600.0;
  static const _taglineStart = 2900.0;
  static const _taglineDur = 500.0;
  static const _fadeoutStart = 3500.0;
  static const _fadeoutDur = 500.0;

  double _norm(double ms) => (ms / _totalMs).clamp(0.0, 1.0);

  @override
  void initState() {
    super.initState();

    _ctrl = AnimationController(
      duration: const Duration(milliseconds: _totalMs),
      vsync: this,
    );

    // GIF fade in + scale
    _gifFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_gifStart), _norm(_gifStart + 400),
            curve: Curves.easeIn),
      ),
    );

    _gifScale = Tween<double>(begin: 0.8, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_gifStart), _norm(_gifStart + _gifDur),
            curve: Curves.easeOutBack),
      ),
    );

    // App name fade + slide up
    _nameFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_nameStart), _norm(_nameStart + _nameDur),
            curve: Curves.easeOut),
      ),
    );

    _nameSlide = Tween<double>(begin: 30.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_nameStart), _norm(_nameStart + _nameDur),
            curve: Curves.easeOutBack),
      ),
    );

    // Tagline fade + slide
    _taglineFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_taglineStart), _norm(_taglineStart + _taglineDur),
            curve: Curves.easeOut),
      ),
    );

    _taglineSlide = Tween<double>(begin: 15.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_taglineStart), _norm(_taglineStart + _taglineDur),
            curve: Curves.easeOut),
      ),
    );

    // Final fade out
    _fadeout = Tween<double>(begin: 1.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(_norm(_fadeoutStart), _norm(_fadeoutStart + _fadeoutDur),
            curve: Curves.easeInOut),
      ),
    );

    _ctrl.forward();
    _navigateAfterDelay();
  }

  Future<void> _navigateAfterDelay() async {
    final auth = context.read<AuthProvider>();
    await auth.init();

    final prefs = await SharedPreferences.getInstance();
    final onboardingDone = prefs.getBool(AppConfig.onboardingKey) ?? false;

    await Future.delayed(const Duration(milliseconds: _totalMs));

    if (!mounted) return;

    if (auth.isAuthenticated) {
      final needsSetup = auth.user?.businessId == null;
      final targetRoute =
          needsSetup ? AppRoutes.businessSetup : AppRoutes.dashboard;
      final lockEnabled = await StorageService().getAppLockEnabled();
      if (!mounted) return;
      if (lockEnabled) {
        Navigator.pushReplacement(
            context,
            MaterialPageRoute(
                builder: (_) => PinLockScreen(targetRoute: targetRoute)));
      } else {
        Navigator.pushReplacementNamed(context, targetRoute);
      }
    } else if (!onboardingDone) {
      Navigator.pushReplacementNamed(context, AppRoutes.onboarding);
    } else {
      Navigator.pushReplacementNamed(context, AppRoutes.login);
    }
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      body: AnimatedBuilder(
        animation: _ctrl,
        builder: (context, _) {
          return Opacity(
            opacity: _fadeout.value,
            child: Container(
              color: Colors.white,
              width: double.infinity,
              height: double.infinity,
              child: Stack(
                alignment: Alignment.center,
                children: [
                  // GIF Animation — center
                  Opacity(
                    opacity: _gifFade.value,
                    child: Transform.scale(
                      scale: _gifScale.value,
                      child: Image.asset(
                        'assets/Duka Rahisi.gif',
                        fit: BoxFit.contain,
                        width: size.width * 0.80,
                      ),
                    ),
                  ),

                  // App name + tagline — bottom area
                  Positioned(
                    bottom: size.height * 0.15,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Animated letters
                        Opacity(
                          opacity: _nameFade.value,
                          child: Transform.translate(
                            offset: Offset(0, _nameSlide.value),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: List.generate(_word.length, (i) {
                                return _AnimatedLetter(
                                  letter: _word[i],
                                  animation: _ctrl,
                                  begin: _norm(_nameStart + i * 40),
                                  end: _norm(_nameStart + i * 40 + 300),
                                );
                              }),
                            ),
                          ),
                        ),
                        const SizedBox(height: 10),
                        // Tagline
                        Transform.translate(
                          offset: Offset(0, _taglineSlide.value),
                          child: Opacity(
                            opacity: _taglineFade.value,
                            child: Text(
                              _tagline.toUpperCase(),
                              style: TextStyle(
                                fontSize: size.width * 0.030,
                                fontWeight: FontWeight.w700,
                                letterSpacing: 2.5,
                                color: AppColors.gold,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

class _AnimatedLetter extends StatelessWidget {
  final String letter;
  final Animation<double> animation;
  final double begin;
  final double end;

  const _AnimatedLetter({
    required this.letter,
    required this.animation,
    required this.begin,
    required this.end,
  });

  @override
  Widget build(BuildContext context) {
    final fade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeIn),
      ),
    );

    final slide = Tween<double>(begin: -20.0, end: 0.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeOutBack),
      ),
    );

    final scale = Tween<double>(begin: 0.3, end: 1.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeOutBack),
      ),
    );

    return AnimatedBuilder(
      animation: animation,
      builder: (context, child) {
        return Opacity(
          opacity: fade.value,
          child: Transform.translate(
            offset: Offset(0, slide.value),
            child: Transform.scale(
              scale: scale.value,
              child: child,
            ),
          ),
        );
      },
      child: Text(
        letter,
        style: TextStyle(
          fontSize: 32,
          fontWeight: FontWeight.w900,
          letterSpacing: -0.5,
          color: AppColors.primary,
          fontFamily: 'Nunito',
        ),
      ),
    );
  }
}
