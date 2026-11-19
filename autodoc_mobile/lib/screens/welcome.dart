import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../services/api_client.dart';
import '../services/auth_service.dart';

class Welcome extends StatefulWidget {
  const Welcome({
    super.key,
    required this.onAuthenticated,
    required this.isDarkMode,
    required this.onToggleTheme,
  });

  final Future<void> Function() onAuthenticated;
  final bool isDarkMode;
  final VoidCallback onToggleTheme;

  @override
  State<Welcome> createState() => _WelcomeState();
}

class _WelcomeState extends State<Welcome> {
  bool _signingIn = false;
  String? _error;

  Future<void> _signIn() async {
    setState(() {
      _signingIn = true;
      _error = null;
    });
    try {
      await AuthService.instance.signInWithMicrosoft();
      if (mounted) await widget.onAuthenticated();
    } on PlatformException catch (error) {
      if (mounted) {
        final detail = error.message?.trim();
        setState(
          () => _error = detail == null || detail.isEmpty
              ? 'Microsoft sign-in failed (${error.code}).'
              : 'Microsoft sign-in failed: $detail',
        );
      }
    } catch (error) {
      if (mounted) {
        setState(
          () =>
              _error = error is ApiException ? error.message : error.toString(),
        );
      }
    } finally {
      if (mounted) setState(() => _signingIn = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: Theme.of(context).colorScheme.surface,
        elevation: 0,
        automaticallyImplyLeading: false,
        shape: const Border(
          bottom: BorderSide(color: Color(0xFF951F1C), width: 3),
        ),
        title: Row(
          children: [
            SvgPicture.asset(
              widget.isDarkMode
                  ? 'assets/logo/autodoc-logo-darkmode.svg'
                  : 'assets/logo/autodoc-logo.svg',
              height: 45,
            ),

            const SizedBox(width: 10),

            Text(
              'AUTO',
              style: TextStyle(
                fontFamily: 'Arial',
                fontSize: 25,
                fontWeight: FontWeight.w600,
                color: Color(0xFFD32F2F),
              ),
            ),

            Text(
              'DOC',
              style: TextStyle(
                fontFamily: 'Arial',
                fontSize: 25,
                fontWeight: FontWeight.w600,
                color: Theme.of(context).colorScheme.onSurface,
              ),
            ),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 15),
            child: IconButton(
              style: IconButton.styleFrom(
                backgroundColor: const Color(0xFFD32F2F),
              ),
              onPressed: widget.onToggleTheme,
              icon: Icon(
                widget.isDarkMode
                    ? Icons.light_mode_outlined
                    : Icons.dark_mode_outlined,
              ),
              color: Colors.white,
            ),
          ),
        ],
      ),

      body: Stack(
        fit: StackFit.expand,
        children: [
          Image.asset(
            widget.isDarkMode
                ? 'assets/images/mcc-bg-dark.png'
                : 'assets/images/mcc-bg-light.png',
            fit: BoxFit.cover,
            alignment: Alignment.center,
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 25,
                    vertical: 20,
                  ),
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 15,
                      vertical: 55,
                    ),
                    decoration: BoxDecoration(
                      color: Theme.of(context).colorScheme.surface
                          .withValues(alpha: widget.isDarkMode ? 0.5 : 0.55),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Center(
                          child: Text(
                            'Sign in to Your Account',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontSize: 25,
                              fontWeight: FontWeight.w400,
                              color: Theme.of(context).colorScheme.onSurface,
                            ),
                          ),
                        ),

                        const SizedBox(height: 14),

                        Center(
                          child: Text(
                            'Please use your official school email.',
                            style: TextStyle(
                              fontSize: 13,
                              color: Theme.of(context).colorScheme.onSurface,
                            ),
                          ),
                        ),

                        const SizedBox(height: 18),

                        if (_error != null) ...[
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFFE8E8),
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(
                                color: const Color(0xFFD32F2F),
                              ),
                            ),
                            child: Text(
                              _error!,
                              textAlign: TextAlign.center,
                              style: const TextStyle(color: Color(0xFF8F1D1D)),
                            ),
                          ),
                          const SizedBox(height: 12),
                        ],

                        Center(
                          child: SizedBox(
                            width: 263,
                            height: 56,
                            child: ElevatedButton(
                              onPressed: _signingIn ? null : _signIn,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFFD32F2F),
                                foregroundColor: Colors.white,
                                elevation: 0,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(14),
                                ),
                              ),
                              child: _signingIn
                                  ? const SizedBox(
                                      width: 22,
                                      height: 22,
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        color: Colors.white,
                                      ),
                                    )
                                  : const Text(
                                      'Login with Microsoft',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 18),

                        Row(
                          children: [
                            Expanded(
                              child: Divider(
                                color: Theme.of(context)
                                    .colorScheme
                                    .onSurfaceVariant,
                                thickness: 1,
                              ),
                            ),

                            const SizedBox(width: 10),

                            Text(
                              'Don’t have an account?',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Theme.of(context).colorScheme.onSurface,
                              ),
                            ),

                            const SizedBox(width: 10),

                            Expanded(
                              child: Divider(
                                color: Theme.of(context)
                                    .colorScheme
                                    .onSurfaceVariant,
                                thickness: 1,
                              ),
                            ),
                          ],
                        ),

                        const SizedBox(height: 7),

                        Center(
                          child: Text(
                            'Contact MIS',
                            style: TextStyle(
                              fontSize: 12,
                              color: Theme.of(context).colorScheme.onSurface,
                            ),
                          ),
                        ),

                        const SizedBox(height: 50),

                        Container(
                          width: double.infinity,
                          height: 3,
                          color: const Color(0xFF951F1C),
                        ),

                        const SizedBox(height: 85),

                        Text(
                          'Student Document Portal',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1,
                            color: Theme.of(context).colorScheme.onSurface,
                          ),
                        ),

                        const SizedBox(height: 5),

                        Text.rich(
                          TextSpan(
                            children: [
                              TextSpan(
                                text: 'Your Documents,\n',
                                style: TextStyle(
                                  color: Theme.of(context)
                                      .colorScheme
                                      .onSurface,
                                ),
                              ),

                              TextSpan(
                                text: 'Verified and Secured.',
                                style: TextStyle(color: Color(0xFFD32F2F)),
                              ),
                            ],
                          ),
                          style: TextStyle(
                            fontSize: 29,
                            height: 0.98,
                            fontWeight: FontWeight.w800,
                          ),
                        ),

                        const SizedBox(height: 8),

                        Text(
                          'A convenient way to upload, verify, and store your '
                          'required documents in one centralized platform.',
                          style: TextStyle(
                            fontSize: 13,
                            height: 1.4,
                            fontWeight: FontWeight.bold,
                            color: Theme.of(context).colorScheme.onSurface,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
