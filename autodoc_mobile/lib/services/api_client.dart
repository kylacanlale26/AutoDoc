import 'dart:async';
import 'dart:convert';

import 'package:file_picker/file_picker.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;

class ApiException implements Exception {
  const ApiException(this.message, [this.statusCode]);

  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient._();

  static final ApiClient instance = ApiClient._();
  static const String baseUrl = String.fromEnvironment(
    'AUTODOC_API_BASE_URL',
    defaultValue: 'http://127.0.0.1:8000',
  );

  static const FlutterSecureStorage _secureStorage = FlutterSecureStorage();
  final http.Client _client = http.Client();
  static const String _tokenKey = 'autodoc_mobile_api_token';
  static const Duration _requestTimeout = Duration(seconds: 15);

  Future<String?> readToken() => _secureStorage.read(key: _tokenKey);

  Future<void> saveToken(String token) =>
      _secureStorage.write(key: _tokenKey, value: token);

  Future<void> clearToken() => _secureStorage.delete(key: _tokenKey);

  Uri _uri(String path) {
    final normalizedBase = baseUrl.replaceFirst(RegExp(r'/+$'), '');
    return Uri.parse('$normalizedBase/api$path');
  }

  Future<Map<String, dynamic>> get(
    String path, {
    bool authenticated = true,
  }) async {
    final response = await _request(() async => _client.get(
          _uri(path),
          headers: await _headers(authenticated: authenticated),
        ));
    return _decode(response);
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
    bool authenticated = true,
  }) async {
    final response = await _request(() async => _client.post(
          _uri(path),
          headers: {
            ...await _headers(authenticated: authenticated),
            'Content-Type': 'application/json',
          },
          body: jsonEncode(body ?? <String, dynamic>{}),
        ));
    return _decode(response);
  }

  Future<Map<String, dynamic>> uploadDocuments({
    required String documentType,
    required List<PlatformFile> files,
  }) async {
    final request = http.MultipartRequest('POST', _uri('/mobile/documents'));
    request.headers.addAll(await _headers());
    request.headers['Accept'] = 'application/json';
    request.fields['document_type'] = documentType;

    for (final file in files) {
      final bytes = file.bytes;
      if (bytes == null) {
        throw const ApiException(
          'Could not read this file. Choose it again and retry.',
        );
      }
      request.files.add(
        http.MultipartFile.fromBytes('documents[]', bytes, filename: file.name),
      );
    }

    final streamed = await _client.send(request);
    final response = await http.Response.fromStream(streamed);
    return _decode(response);
  }

  Future<Map<String, dynamic>> uploadProfile({
    required String studentNumber,
    required String course,
    required String yearLevel,
    required String section,
    required PlatformFile picture,
  }) async {
    final bytes = picture.bytes;
    if (bytes == null) {
      throw const ApiException('Could not read the selected profile picture.');
    }
    final request = http.MultipartRequest('POST', _uri('/mobile/profile'));
    request.headers.addAll(await _headers());
    request.headers['Accept'] = 'application/json';
    request.fields.addAll({
      'student_number': studentNumber,
      'course': course,
      'year_level': yearLevel,
      'section': section,
      'terms_accepted': '1',
    });
    request.files.add(
      http.MultipartFile.fromBytes(
        'profile_picture',
        bytes,
        filename: picture.name,
      ),
    );
    final response = await _client.send(request);
    return _decode(await http.Response.fromStream(response));
  }

  Future<Map<String, String>> _headers({bool authenticated = true}) async {
    final headers = <String, String>{'Accept': 'application/json'};
    if (authenticated) {
      final token = await readToken();
      if (token == null || token.isEmpty) {
        throw const ApiException('Sign in to continue.', 401);
      }
      headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  Future<http.Response> _request(
    Future<http.Response> Function() send,
  ) async {
    try {
      return await send().timeout(_requestTimeout);
    } on TimeoutException {
      throw const ApiException(
        'AutoDoc’s server did not respond in time. Check that Laravel is running and your phone and PC are on the same Wi-Fi.',
      );
    } on http.ClientException {
      throw const ApiException(
        'Could not reach AutoDoc’s server. Check the API address, Laravel server, and phone/PC Wi-Fi connection.',
      );
    }
  }

  Map<String, dynamic> _decode(http.Response response) {
    dynamic decoded;
    try {
      decoded = response.body.isEmpty
          ? <String, dynamic>{}
          : jsonDecode(response.body);
    } on FormatException {
      decoded = <String, dynamic>{};
    }

    if (response.statusCode < 200 || response.statusCode >= 300) {
      var message = decoded is Map<String, dynamic>
          ? decoded['message']?.toString()
          : null;
      final errors = decoded is Map<String, dynamic> ? decoded['errors'] : null;
      if (errors is Map && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) message = first.first.toString();
      }
      throw ApiException(
        message ?? 'The server returned an error (${response.statusCode}).',
        response.statusCode,
      );
    }

    if (decoded is Map<String, dynamic>) return decoded;
    throw const ApiException('The server returned an unexpected response.');
  }
}
