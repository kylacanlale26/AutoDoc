import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import 'dashboard.dart';

class AuthenticationPage extends StatefulWidget {
  const AuthenticationPage({
    super.key,
    required this.email,
    this.isDarkMode = false,
    this.onVerifyCode,
    this.onRequestNewCode,
  });

  final String email;
  final bool isDarkMode;
  final ValueChanged<String>? onVerifyCode;
  final VoidCallback? onRequestNewCode;

  @override
  State<AuthenticationPage> createState() => _AuthenticationPageState();
}

class _AuthenticationPageState extends State<AuthenticationPage> {
  static const _brandRed = Color(0xFFB12A2A);
  final _controllers = List.generate(4, (_) => TextEditingController());
  final _focusNodes = List.generate(4, (_) => FocusNode());
  String? _errorMessage;
  bool _isCodeAccepted = false;

  @override
  void dispose() {
    for (final controller in _controllers) {
      controller.dispose();
    }
    for (final focusNode in _focusNodes) {
      focusNode.dispose();
    }
    super.dispose();
  }

  void _verifyCode() {
    final code = _controllers.map((controller) => controller.text).join();
    if (code.length != _controllers.length) {
      setState(() => _errorMessage = 'Enter all four digits.');
      return;
    }

    FocusManager.instance.primaryFocus?.unfocus();
    widget.onVerifyCode?.call(code);
    Navigator.of(context).pushReplacement<void, void>(
      MaterialPageRoute<void>(
        builder: (_) =>
            DashboardPage(email: widget.email, isDarkMode: widget.isDarkMode),
      ),
    );
  }

  void _requestNewCode() {
    if (widget.onRequestNewCode == null) {
      _showPrototypeMessage('Email code delivery is not connected yet.');
      return;
    }

    widget.onRequestNewCode!();
  }

  void _showPrototypeMessage(String message) {
    ScaffoldMessenger.of(context)
        .showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    final isDark = widget.isDarkMode;
    final foreground = isDark ? Colors.white : Colors.black;
    final background = isDark ? const Color(0xFF121212) : Colors.white;

    return Scaffold(
      backgroundColor: background,
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 24),
              child: ConstrainedBox(
                constraints: BoxConstraints(
                  minHeight: constraints.maxHeight - 48,
                ),
                child: Center(
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 360),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          'Authentication',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: foreground,
                            fontSize: 36,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          'Enter the code for ${widget.email}.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: foreground, fontSize: 16),
                        ),
                        const SizedBox(height: 24),
                        Row(
                          children: List.generate(_controllers.length, (index) {
                            return Expanded(
                              child: Padding(
                                padding: EdgeInsets.only(
                                  right: index == _controllers.length - 1
                                      ? 0
                                      : 14,
                                ),
                                child: SizedBox(
                                  height: 74,
                                  child: TextField(
                                    controller: _controllers[index],
                                    focusNode: _focusNodes[index],
                                    keyboardType: TextInputType.number,
                                    textAlign: TextAlign.center,
                                    maxLength: 1,
                                    inputFormatters: [
                                      FilteringTextInputFormatter.digitsOnly,
                                    ],
                                    style: TextStyle(
                                      color: foreground,
                                      fontSize: 28,
                                    ),
                                    decoration: InputDecoration(
                                      counterText: '',
                                      contentPadding: EdgeInsets.zero,
                                      enabledBorder: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(8),
                                        borderSide: BorderSide(
                                          color: foreground,
                                          width: 2,
                                        ),
                                      ),
                                      focusedBorder: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(8),
                                        borderSide: const BorderSide(
                                          color: _brandRed,
                                          width: 2,
                                        ),
                                      ),
                                    ),
                                    onChanged: (value) {
                                      setState(() {
                                        _errorMessage = null;
                                        _isCodeAccepted = false;
                                      });
                                      if (value.isNotEmpty &&
                                          index < _focusNodes.length - 1) {
                                        _focusNodes[index + 1].requestFocus();
                                      } else if (value.isEmpty && index > 0) {
                                        _focusNodes[index - 1].requestFocus();
                                      }
                                    },
                                  ),
                                ),
                              ),
                            );
                          }),
                        ),
                        if (_errorMessage != null) ...[
                          const SizedBox(height: 8),
                          Text(
                            _errorMessage!,
                            textAlign: TextAlign.center,
                            style: const TextStyle(color: _brandRed),
                          ),
                        ],
                        if (_isCodeAccepted) ...[
                          const SizedBox(height: 8),
                          const Text(
                            'Code accepted for now.',
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Color(0xFF218838)),
                          ),
                        ],
                        Align(
                          alignment: Alignment.centerRight,
                          child: Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              Text(
                                'Can’t see code?',
                                style: TextStyle(
                                  color: foreground,
                                  fontSize: 16,
                                ),
                              ),
                              TextButton(
                                onPressed: _requestNewCode,
                                style: TextButton.styleFrom(
                                  foregroundColor: _brandRed,
                                  padding: const EdgeInsets.only(left: 8),
                                ),
                                child: const Text(
                                  'Request new code.',
                                  style: TextStyle(fontSize: 16),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 18),
                        SizedBox(
                          height: 54,
                          child: DecoratedBox(
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                begin: Alignment.topCenter,
                                end: Alignment.bottomCenter,
                                colors: [Color(0xFFA91616), Color(0xFFD91F26)],
                              ),
                              borderRadius: BorderRadius.circular(7),
                            ),
                            child: ElevatedButton(
                              onPressed: _verifyCode,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.transparent,
                                foregroundColor: Colors.white,
                                shadowColor: Colors.transparent,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(7),
                                ),
                              ),
                              child: const Text(
                                'Verify Code',
                                style: TextStyle(fontSize: 20),
                              ),
                            ),
                          ),
                        ),
                      ],
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
}
