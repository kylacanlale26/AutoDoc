import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api',
  );

  static Future<Map<String, dynamic>> login(
    String email,
    String password,
  ) async {
    final response = await http
        .post(
          Uri.parse('$baseUrl/login'),
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: jsonEncode({
            'email': email,
            'password': password,
          }),
        )
        .timeout(const Duration(seconds: 15));

    final Object? decoded;
    try {
      decoded = jsonDecode(response.body);
    } on FormatException {
      throw const ApiException('The server returned an invalid response.');
    }

    if (decoded is! Map<String, dynamic>) {
      throw const ApiException('The server returned an invalid response.');
    } else {
      if (response.statusCode >= 200 && response.statusCode < 300) {
        return decoded;
      }

      throw ApiException(
        decoded['message'] as String? ?? 'Login failed.',
      );
    }
  }
}

class ApiException implements Exception {
  const ApiException(this.message);

  final String message;
}