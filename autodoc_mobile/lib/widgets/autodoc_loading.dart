import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

/// The shared AutoDoc loading view, matching the Laravel web loader.
class AutoDocLoading extends StatefulWidget {
  const AutoDocLoading({super.key});

  @override
  State<AutoDocLoading> createState() => _AutoDocLoadingState();
}

class _AutoDocLoadingState extends State<AutoDocLoading>
    with SingleTickerProviderStateMixin {
  late final AnimationController _bobController;

  @override
  void initState() {
    super.initState();
    _bobController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 750),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _bobController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDarkMode = Theme.of(context).brightness == Brightness.dark;

    return ColoredBox(
      color: isDarkMode ? const Color(0xFF111315) : Colors.white,
      child: Semantics(
        label: 'Loading AutoDoc',
        liveRegion: true,
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              AnimatedBuilder(
                animation: _bobController,
                builder: (context, child) {
                  final bob = Curves.easeInOut.transform(_bobController.value);
                  return Transform.translate(
                    offset: Offset(0, -5 * bob),
                    child: child,
                  );
                },
                child: SizedBox(
                  width: 92,
                  height: 92,
                  child: SvgPicture.asset(
                    isDarkMode
                        ? 'assets/logo/autodoc-logo-darkmode.svg'
                        : 'assets/logo/autodoc-logo.svg',
                  ),
                ),
              ),
              const SizedBox(height: 14),
              Text(
                'Loading AutoDoc',
                style: TextStyle(
                  fontFamily: 'Arial',
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  letterSpacing: 0.56,
                  color: isDarkMode
                      ? const Color(0xFFECEFF1)
                      : const Color(0xFF242424),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
