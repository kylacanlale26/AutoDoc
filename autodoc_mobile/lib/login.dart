import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import 'authentication.dart';
import 'services/api_service.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({
    super.key,
    this.onSignIn,
    this.onForgotPassword,
    this.isDarkMode = false,
  });

  final ValueChanged<LoginCredentials>? onSignIn;
  final VoidCallback? onForgotPassword;
  final bool isDarkMode;

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class LoginCredentials {
  const LoginCredentials({required this.email, required this.password});

  final String email;
  final String password;
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  bool _passwordVisible = false;
  bool _isSubmitting = false;
  String? _errorMessage;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_isSubmitting || !_formKey.currentState!.validate()) return;

    final credentials = LoginCredentials(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    try {
      await ApiService.login(credentials.email, credentials.password);
      if (!mounted) return;

      widget.onSignIn?.call(credentials);
      await Navigator.of(context).push<void>(
        MaterialPageRoute<void>(
          builder: (_) => AuthenticationPage(
            email: credentials.email,
            isDarkMode: widget.isDarkMode,
          ),
        ),
      );
    } on ApiException catch (error) {
      if (!mounted) return;
      setState(() => _errorMessage = error.message);
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _errorMessage = 'Unable to reach the server. Check your connection.';
      });
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = widget.isDarkMode;
    final foreground = isDark ? Colors.white : const Color(0xFF111111);
    final fieldFill = isDark ? const Color(0xFF242424) : Colors.white;

    return Scaffold(
      backgroundColor: isDark
          ? const Color(0xFF121212)
          : const Color(0xFFF8F8F8),
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 28),
              child: ConstrainedBox(
                constraints: BoxConstraints(
                  minHeight: constraints.maxHeight - 56,
                ),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 500),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Align(
                            alignment: Alignment.centerLeft,
                            child: IconButton(
                              tooltip: 'Back',
                              onPressed: () => Navigator.of(context).maybePop(),
                              icon: Icon(Icons.arrow_back, color: foreground),
                            ),
                          ),
                          Center(
                            child: RichText(
                              text: TextSpan(
                                style: GoogleFonts.raleway(
                                  fontSize: 67,
                                  height: 1,
                                  fontWeight: FontWeight.w700,
                                ),
                                children: [
                                  const TextSpan(
                                    text: 'AUTO',
                                    style: TextStyle(color: Color(0xFFB12A2A)),
                                  ),
                                  TextSpan(
                                    text: 'DOC',
                                    style: TextStyle(color: foreground),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 27),
                          Text(
                            'Sign in to your MCC Account',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              color: foreground,
                              fontSize: 18,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          const SizedBox(height: 22),
                          if (_errorMessage != null) ...[
                            Text(
                              _errorMessage!,
                              style: const TextStyle(
                                color: Color(0xFFB42323),
                                fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 12),
                          ],
                          _buildLabel('Email', foreground),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _emailController,
                            keyboardType: TextInputType.emailAddress,
                            textInputAction: TextInputAction.next,
                            autofillHints: const [AutofillHints.email],
                            style: TextStyle(color: foreground, fontSize: 15),
                            decoration: _inputDecoration(
                              hint: 'Enter your email',
                              foreground: foreground,
                              fill: fieldFill,
                            ),
                            validator: (value) {
                              final email = value?.trim() ?? '';
                              if (email.isEmpty) return 'Enter your email';
                              if (!email.contains('@')) {
                                return 'Enter a valid email';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: 13),
                          _buildLabel('Password', foreground),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _passwordController,
                            obscureText: !_passwordVisible,
                            textInputAction: TextInputAction.done,
                            autofillHints: const [AutofillHints.password],
                            onFieldSubmitted: (_) => _submit(),
                            style: TextStyle(color: foreground, fontSize: 15),
                            decoration:
                                _inputDecoration(
                                  hint: 'Enter your password',
                                  foreground: foreground,
                                  fill: fieldFill,
                                ).copyWith(
                                  suffixIcon: IconButton(
                                    tooltip: _passwordVisible
                                        ? 'Hide password'
                                        : 'Show password',
                                    onPressed: () {
                                      setState(() {
                                        _passwordVisible = !_passwordVisible;
                                      });
                                    },
                                    icon: Icon(
                                      _passwordVisible
                                          ? Icons.visibility_outlined
                                          : Icons.visibility_off_outlined,
                                      color: foreground.withValues(alpha: 0.85),
                                    ),
                                  ),
                                ),
                            validator: (value) {
                              if (value == null || value.isEmpty) {
                                return 'Enter your password';
                              }
                              return null;
                            },
                          ),
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton(
                              onPressed: widget.onForgotPassword,
                              style: TextButton.styleFrom(
                                foregroundColor: foreground,
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 0,
                                  vertical: 7,
                                ),
                              ),
                              child: const Text(
                                'Forgot password?',
                                style: TextStyle(fontSize: 14),
                              ),
                            ),
                          ),
                          const SizedBox(height: 8),
                          SizedBox(
                            height: 38,
                            child: DecoratedBox(
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  begin: Alignment.topCenter,
                                  end: Alignment.bottomCenter,
                                  colors: [
                                    Color(0xFFA91616),
                                    Color(0xFFD91F26),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: ElevatedButton(
                                onPressed: _isSubmitting ? null : _submit,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.transparent,
                                  foregroundColor: Colors.white,
                                  shadowColor: Colors.transparent,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                ),
                                child: _isSubmitting
                                    ? const SizedBox(
                                        width: 18,
                                        height: 18,
                                        child: CircularProgressIndicator(
                                          strokeWidth: 2,
                                          color: Colors.white,
                                        ),
                                      )
                                    : const Text(
                                        'Sign In',
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 20),
                          Row(
                            children: [
                              Expanded(
                                child: Divider(
                                  color: isDark
                                      ? const Color(0xFF777777)
                                      : const Color(0xFF555555),
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 20,
                                ),
                                child: Text(
                                  "Don't have an account?",
                                  style: TextStyle(
                                    color: isDark
                                        ? const Color(0xFFAAAAAA)
                                        : const Color(0xFF666666),
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                              Expanded(
                                child: Divider(
                                  color: isDark
                                      ? const Color(0xFF777777)
                                      : const Color(0xFF555555),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 11),
                          Text(
                            'Contact MIS',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              color: isDark
                                  ? const Color(0xFFAAAAAA)
                                  : const Color(0xFF666666),
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildLabel(String label, Color color) {
    return Text(
      label,
      style: TextStyle(color: color, fontSize: 15, fontWeight: FontWeight.w700),
    );
  }

  InputDecoration _inputDecoration({
    required String hint,
    required Color foreground,
    required Color fill,
  }) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: Color(0xFF888888), fontSize: 12),
      filled: true,
      fillColor: fill,
      contentPadding: const EdgeInsets.symmetric(horizontal: 9, vertical: 11),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide(
          color: widget.isDarkMode
              ? const Color(0xFFDDDDDD)
              : const Color(0xFF222222),
          width: 2,
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Color(0xFFA51F1F), width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Color(0xFFB42323), width: 2),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Color(0xFFB42323), width: 2),
      ),
    );
  }
}
