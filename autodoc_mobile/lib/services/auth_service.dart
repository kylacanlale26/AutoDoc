import 'dart:async';
import 'dart:io' show Platform;

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter_appauth/flutter_appauth.dart';

import 'api_client.dart';

class AuthService {
  AuthService._();

  static final AuthService instance = AuthService._();
  final FlutterAppAuth _appAuth = FlutterAppAuth();
  final ApiClient _api = ApiClient.instance;

  Future<void> signInWithMicrosoft() async {
    if (kIsWeb) {
      throw const ApiException(
        'Microsoft sign-in is not available in the browser preview. Run AutoDoc on an iPhone or Android device to sign in.',
      );
    }

    if (Platform.isIOS &&
        (ApiClient.baseUrl.contains('127.0.0.1') ||
            ApiClient.baseUrl.contains('localhost'))) {
      throw const ApiException(
        'This iPhone build points to itself instead of your computer. Rebuild it with AUTODOC_API_BASE_URL set to your computer’s Wi-Fi IP address, such as http://192.168.1.20:8000.',
      );
    }

    final config = await _api.get('/mobile/auth/config', authenticated: false);
    final clientId = config['client_id']?.toString() ?? '';
    final tenant = config['tenant']?.toString() ?? 'organizations';
    final redirectUri =
        (Platform.isIOS
                ? config['ios_redirect_uri']
                : config['android_redirect_uri'])
            ?.toString();

    if (clientId.isEmpty || redirectUri == null || redirectUri.isEmpty) {
      throw const ApiException(
        'Microsoft mobile sign-in is not configured. Add the mobile redirect URI in Laravel and Microsoft Entra.',
      );
    }

    final authResult = await _appAuth.authorizeAndExchangeCode(
      AuthorizationTokenRequest(
        clientId,
        redirectUri,
        discoveryUrl:
            'https://login.microsoftonline.com/$tenant/v2.0/.well-known/openid-configuration',
        scopes: const [
          'openid',
          'profile',
          'email',
          'offline_access',
          'User.Read',
        ],
        additionalParameters: const {'prompt': 'select_account'},
      ),
    ).timeout(
      const Duration(seconds: 60),
      onTimeout: () => throw const ApiException(
        'Microsoft sign-in took too long. Check the phone’s internet connection, then try again.',
      ),
    );

    final microsoftAccessToken = authResult.accessToken;
    if (microsoftAccessToken == null || microsoftAccessToken.isEmpty) {
      throw const ApiException('Microsoft did not return an access token.');
    }

    final session = await _api.post(
      '/mobile/auth/microsoft',
      authenticated: false,
      body: {'access_token': microsoftAccessToken},
    );
    final apiToken = session['token']?.toString();
    if (apiToken == null || apiToken.isEmpty) {
      throw const ApiException('AutoDoc did not issue a mobile session.');
    }

    await _api.saveToken(apiToken);
  }

  Future<void> signOut() async {
    try {
      await _api.post('/mobile/auth/logout');
    } catch (_) {
      // Clear the local credential even if the phone is offline or the API is unavailable.
    } finally {
      await _api.clearToken();
    }
  }
}
