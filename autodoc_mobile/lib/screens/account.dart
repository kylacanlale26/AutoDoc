import 'package:flutter/material.dart';

import '../services/api_client.dart';
import '../widgets/autodoc_loading.dart';
import '../widgets/profile_avatar.dart';

class Account extends StatefulWidget {
  const Account({super.key, this.onLoadingChanged});
  final ValueChanged<bool>? onLoadingChanged;

  @override
  State<Account> createState() => _AccountState();
}

class _AccountState extends State<Account> {
  Map<String, dynamic> user = {};
  List<Map<String, dynamic>> documents = [];
  List<String> types = [];
  bool loading = true;
  String? error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    _notifyLoading(user.isEmpty);
    try {
      final results = await Future.wait([
        ApiClient.instance.get('/mobile/me'),
        ApiClient.instance.get('/mobile/documents'),
        ApiClient.instance.get('/mobile/document-types'),
      ]);
      user = results[0];
      documents = List<Map<String, dynamic>>.from(
        (results[1]['documents'] as List? ?? const []).whereType<Map>().map(
          (e) => Map<String, dynamic>.from(e),
        ),
      );
      types = List<String>.from(
        (results[2]['document_types'] as List? ?? const []).map(
          (e) => e.toString(),
        ),
      );
    } catch (e) {
      error = e.toString();
    } finally {
      if (mounted) {
        setState(() => loading = false);
        _notifyLoading(false);
      }
    }
  }

  void _notifyLoading(bool isLoading) {
    final callback = widget.onLoadingChanged;
    if (callback == null) return;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) callback(isLoading);
    });
  }

  String val(dynamic v) =>
      v?.toString().trim().isNotEmpty == true ? v.toString() : 'Not provided';

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final fullName = [user['first_name'], user['last_name']]
        .where((v) => v != null && v.toString().trim().isNotEmpty)
        .join(' ');
    final submittedTypes = documents
        .map((d) => d['document_type']?.toString())
        .whereType<String>()
        .toSet();
    final verified = documents
        .where((d) => d['status']?.toString().toLowerCase() == 'verified')
        .toList();
    final pending = documents
        .where(
          (d) =>
              d['status']?.toString().toLowerCase().replaceAll(' ', '_') ==
              'needs_correction',
        )
        .toList();
    final unsubmitted = types
        .where((t) => !submittedTypes.contains(t))
        .toList();

    if (loading && user.isEmpty) {
      return const SafeArea(child: AutoDocLoading());
    }

    return SafeArea(
      child: RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(15),
          children: [
            Text(
              'My Account',
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            if (error != null)
              Padding(
                padding: const EdgeInsets.only(top: 12),
                child: Text(error!, style: TextStyle(color: scheme.error)),
              ),
            ...[
              const SizedBox(height: 24),
              Center(
                child: ProfileAvatar(
                  imageUrl: user['profile_picture_url']?.toString(),
                  radius: 56,
                  iconSize: 55,
                ),
              ),
              const SizedBox(height: 10),
              Center(
                child: Text(
                  fullName.isEmpty ? val(user['name']) : fullName,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
              const SizedBox(height: 18),
              _field(context, 'Student Number', val(user['student_number'])),
              _field(context, 'Email', val(user['email'])),
              _field(context, 'Program', val(user['course'])),
              _field(context, 'Year Level', val(user['year_level'])),
              _field(context, 'Section', val(user['section'])),
              const SizedBox(height: 15),
              _docSection(
                context,
                'Verified Documents',
                verified,
                'No verified documents yet.',
              ),
              _docSection(
                context,
                'Needs Correction',
                pending,
                'No documents need correction.',
              ),
              _simpleSection(
                context,
                'Unsubmitted Documents',
                unsubmitted,
                'All document types have been submitted.',
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _field(BuildContext c, String label, String value) => Container(
    margin: const EdgeInsets.only(top: 7),
    padding: const EdgeInsets.all(13),
    decoration: BoxDecoration(
      color: Theme.of(c).colorScheme.surface,
      borderRadius: BorderRadius.circular(12),
      border: Border.all(color: Theme.of(c).dividerColor),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            color: Theme.of(c).colorScheme.onSurfaceVariant,
            fontSize: 12,
          ),
        ),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600)),
      ],
    ),
  );

  Widget _docSection(
    BuildContext c,
    String title,
    List<Map<String, dynamic>> list,
    String empty,
  ) => _simpleSection(
    c,
    title,
    list
        .map(
          (d) =>
              '${d['document_type'] ?? 'Document'} — ${d['document_name'] ?? ''}',
        )
        .toList(),
    empty,
  );

  Widget _simpleSection(
    BuildContext c,
    String title,
    List<String> list,
    String empty,
  ) => Padding(
    padding: const EdgeInsets.only(top: 18),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: Theme.of(c).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 7),
        if (list.isEmpty)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Theme.of(c).colorScheme.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Theme.of(c).dividerColor),
            ),
            child: Text(empty),
          )
        else
          ...list.map(
            (item) => Container(
              width: double.infinity,
              margin: const EdgeInsets.only(bottom: 7),
              padding: const EdgeInsets.all(13),
              decoration: BoxDecoration(
                color: Theme.of(c).colorScheme.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Theme.of(c).dividerColor),
              ),
              child: Text(item),
            ),
          ),
      ],
    ),
  );
}
