import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../routes/app_routes.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _logoDrop;
  late Animation<double> _logoOpacity;
  late Animation<double> _squash;
  late Animation<double> _shadowGrow;
  late Animation<double> _slideLeft;
  late Animation<double> _taglineFade;
  late Animation<double> _taglineSlide;
  late Animation<double> _fadeout;

  static const _word = 'wazabiashara';
  static const _tagline = 'Biashara Yako, Mkononi Mwako';
  static const _totalMs = 5000;

  // Timeline (ms)
  static const _dropStart = 150.0;
  static const _dropDur = 1050.0;
  static const _shadowStart = 1050.0;
  static const _shadowDur = 500.0;
  static const _squashStart = 1050.0;
  static const _squashDur = 420.0;
  static const _slideStart = 1750.0;
  static const _slideDur = 900.0;
  static const _letterBase = 1850.0;
  static const _letterStep = 80.0;
  static const _letterDur = 500.0;
  static const _taglineStart = 3050.0;
  static const _taglineDur = 700.0;
  static const _fadeoutStart = 4200.0;
  static const _fadeoutDur = 600.0;

  double _norm(double ms) => (ms / _totalMs).clamp(0.0, 1.0);

  @override
  void initState() {
    super.initState();

    _ctrl = AnimationController(
      duration: const Duration(milliseconds: _totalMs),
      vsync: this,
    );

    // Logo drop: translateY -420 -> 0 with bounce
    _logoDrop = Tween<double>(begin: -420.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_dropStart),
          _norm(_dropStart + _dropDur),
          curve: Curves.easeOutBack,
        ),
      ),
    );

    _logoOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_dropStart),
          _norm(_dropStart + _dropDur * 0.3),
          curve: Curves.easeIn,
        ),
      ),
    );

    // Squash: scale bounce on impact
    _squash = TweenSequence<double>([
      TweenSequenceItem(
        tween: Tween(begin: 1.0, end: 1.18)
            .chain(CurveTween(curve: Curves.easeOut)),
        weight: 30,
      ),
      TweenSequenceItem(
        tween: Tween(begin: 1.18, end: 0.94)
            .chain(CurveTween(curve: Curves.easeInOut)),
        weight: 25,
      ),
      TweenSequenceItem(
        tween: Tween(begin: 0.94, end: 1.04)
            .chain(CurveTween(curve: Curves.easeInOut)),
        weight: 20,
      ),
      TweenSequenceItem(
        tween: Tween(begin: 1.04, end: 1.0)
            .chain(CurveTween(curve: Curves.easeOut)),
        weight: 25,
      ),
    ]).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_squashStart),
          _norm(_squashStart + _squashDur),
        ),
      ),
    );

    // Shadow grow
    _shadowGrow = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_shadowStart),
          _norm(_shadowStart + _shadowDur),
          curve: Curves.easeOut,
        ),
      ),
    );

    // Slide left
    _slideLeft = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_slideStart),
          _norm(_slideStart + _slideDur),
          curve: Curves.easeInOutCubic,
        ),
      ),
    );

    // Tagline
    _taglineFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_taglineStart),
          _norm(_taglineStart + _taglineDur),
          curve: Curves.easeOut,
        ),
      ),
    );

    _taglineSlide = Tween<double>(begin: 12.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_taglineStart),
          _norm(_taglineStart + _taglineDur),
          curve: Curves.easeOut,
        ),
      ),
    );

    // Fade out everything
    _fadeout = Tween<double>(begin: 1.0, end: 0.0).animate(
      CurvedAnimation(
        parent: _ctrl,
        curve: Interval(
          _norm(_fadeoutStart),
          _norm(_fadeoutStart + _fadeoutDur),
          curve: Curves.easeOut,
        ),
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
      Navigator.pushReplacementNamed(context, AppRoutes.dashboard);
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
    final logoSize = size.width * 0.32;
    final slideOffset = logoSize * 0.65;

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
                  // Shadow
                  Positioned(
                    top: size.height * 0.5 + logoSize * 0.45,
                    child: Transform.scale(
                      scaleX: 0.15 + _shadowGrow.value * 0.85,
                      scaleY: 1.0,
                      child: Container(
                        width: logoSize * 0.85,
                        height: 16,
                        decoration: BoxDecoration(
                          shape: BoxShape.ellipse,
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.16 * _shadowGrow.value),
                              blurRadius: 12,
                              spreadRadius: 2,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),

                  // Logo + Wordmark row
                  Transform.translate(
                    offset: Offset(
                      -_slideLeft.value * slideOffset,
                      _logoDrop.value,
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // Logo
                        Opacity(
                          opacity: _logoOpacity.value,
                          child: Transform.scale(
                            scaleX: _squash.value,
                            scaleY: 2.0 - _squash.value,
                            child: Container(
                              width: logoSize,
                              height: logoSize,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(logoSize * 0.22),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withValues(alpha: 0.18),
                                    blurRadius: 22,
                                    offset: const Offset(0, 14),
                                  ),
                                ],
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(logoSize * 0.22),
                                child: Image.asset(
                                  'assets/images/logo.png',
                                  fit: BoxFit.contain,
                                ),
                              ),
                            ),
                          ),
                        ),

                        SizedBox(width: logoSize * 0.12),

                        // Wordmark — letter by letter
                        Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              crossAxisAlignment: CrossAxisAlignment.baseline,
                              children: List.generate(_word.length, (i) {
                                return _AnimatedLetter(
                                  letter: _word[i],
                                  animation: _ctrl,
                                  begin: _norm(_letterBase + i * _letterStep),
                                  end: _norm(_letterBase + i * _letterStep + _letterDur),
                                );
                              }),
                            ),
                            SizedBox(height: logoSize * 0.08),
                            // Tagline
                            Transform.translate(
                              offset: Offset(0, _taglineSlide.value),
                              child: Opacity(
                                opacity: _taglineFade.value,
                                child: Text(
                                  _tagline.toUpperCase(),
                                  style: TextStyle(
                                    fontSize: size.width * 0.028,
                                    fontWeight: FontWeight.w700,
                                    letterSpacing: 2.5,
                                    color: AppColors.gold,
                                  ),
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

    final slide = Tween<double>(begin: -26.0, end: 0.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeOutBack),
      ),
    );

    final scale = Tween<double>(begin: 0.4, end: 1.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeOutBack),
      ),
    );

    final rotate = Tween<double>(begin: -0.24, end: 0.0).animate(
      CurvedAnimation(
        parent: animation,
        curve: Interval(begin, end, curve: Curves.easeOut),
      ),
    );

    return AnimatedBuilder(
      animation: animation,
      builder: (context, child) {
        return Opacity(
          opacity: fade.value,
          child: Transform.translate(
            offset: Offset(0, slide.value),
            child: Transform.rotate(
              angle: rotate.value,
              child: Transform.scale(
                scale: scale.value,
                child: child,
              ),
            ),
          ),
        );
      },
      child: Text(
        letter,
        style: TextStyle(
          fontSize: 48,
          fontWeight: FontWeight.w900,
          letterSpacing: -1.0,
          color: AppColors.primary,
          fontFamily: 'Nunito',
        ),
      ),
    );
  }
}
