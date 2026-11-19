import 'package:flutter/material.dart';

import '../services/api_client.dart';

class Dashboard extends StatefulWidget {
  const Dashboard({
    super.key,
    required this.onUpload,
    required this.onOpenDocument,
  });

  final VoidCallback onUpload;
  final Future<void> Function(String id, {String? message}) onOpenDocument;

  @override
  State<Dashboard> createState() => _DashboardState();
}

class _DashboardState extends State<Dashboard> {
  Map<String, dynamic> _data = {};
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      _data = await ApiClient.instance.get('/mobile/dashboard');
    } catch (error) {
      _error = error.toString();
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final student = _data['student'] is Map
        ? Map<String, dynamic>.from(_data['student'] as Map)
        : <String, dynamic>{};
    final counts = _data['counts'] is Map
        ? Map<String, dynamic>.from(_data['counts'] as Map)
        : <String, dynamic>{};
    final recent = _data['recent_documents'] is List
        ? List<Map<String, dynamic>>.from(
            (_data['recent_documents'] as List).whereType<Map>().map(
              Map<String, dynamic>.from,
            ),
          )
        : <Map<String, dynamic>>[];

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _load,
          child: ListView(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            children: [
              Text(
                'Hello, ${student['first_name'] ?? student['name'] ?? 'Student'}!',
                style: Theme.of(context).textTheme.headlineSmall
                    ?.copyWith(fontSize: 27, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 4),
              Text(
                'Manage your documents in one place.',
                style: Theme.of(context).textTheme.bodyMedium,
              ),
              if (_error != null) ...[
                const SizedBox(height: 12),
                _MessageBox(message: _error!, onRetry: _load),
              ],
              if (_loading && _data.isEmpty)
                const Padding(
                  padding: EdgeInsets.all(36),
                  child: Center(child: CircularProgressIndicator()),
                )
              else ...[
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: _StatCard(
                        icon: Icons.description_outlined,
                        color: const Color(0xFF951F1C),
                        count: counts['total'] ?? 0,
                        label: 'Total Documents',
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _StatCard(
                        icon: Icons.check_circle_outline,
                        color: const Color(0xFF007A3F),
                        count: counts['verified'] ?? 0,
                        label: 'Verified',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: _StatCard(
                        icon: Icons.schedule_outlined,
                        color: const Color(0xFFFFB900),
                        count: counts['pending'] ?? 0,
                        label: 'Pending',
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _StatCard(
                        icon: Icons.error_outline,
                        color: const Color(0xFF951F1C),
                        count: counts['needs_correction'] ?? 0,
                        label: 'Needs Correction',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                SizedBox(
                  height: 50,
                  child: ElevatedButton.icon(
                    onPressed: widget.onUpload,
                    icon: const Icon(Icons.file_upload_outlined),
                    label: const Text('Upload a Document'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFD32F2F),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(7),
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 22),
                const Text(
                  'Recent Documents',
                  style: TextStyle(fontSize: 21, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                if (recent.isEmpty)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 25),
                    child: Center(child: Text('No Recent Submissions')),
                  )
                else
                  ...recent.map(
                    (document) => Container(
                      margin: const EdgeInsets.symmetric(vertical: 5),
                      decoration: BoxDecoration(
                        color: Theme.of(context).colorScheme.surface,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: Theme.of(context).dividerColor,
                        ),
                      ),
                      child: ListTile(
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        title: Text(
                          document['document_type']?.toString() ?? 'Document',
                          style: const TextStyle(fontWeight: FontWeight.w700),
                        ),
                        subtitle: Text(
                          document['document_name']?.toString() ?? '',
                        ),
                        trailing: _StatusChip(
                          status: document['status']?.toString() ?? 'pending',
                        ),
                        onTap: () async {
                          await widget.onOpenDocument(
                            document['id'].toString(),
                          );
                          await _load();
                        },
                      ),
                    ),
                  ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  const _StatCard({
    required this.icon,
    required this.color,
    required this.count,
    required this.label,
  });

  final IconData icon;
  final Color color;
  final Object count;
  final String label;

  @override
  Widget build(BuildContext context) => Container(
    height: 130,
    padding: const EdgeInsets.all(13),
    decoration: BoxDecoration(
      color: Theme.of(context).colorScheme.surfaceContainerHighest,
      borderRadius: BorderRadius.circular(14),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Icon(icon, color: color),
        Text(
          '$count',
          style: Theme.of(context).textTheme.titleLarge
              ?.copyWith(fontWeight: FontWeight.bold),
        ),
        Text(
          label,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: Theme.of(context).textTheme.bodySmall,
        ),
      ],
    ),
  );
}

class _StatusChip extends StatelessWidget {
  const _StatusChip({required this.status});
  final String status;

  @override
  Widget build(BuildContext context) {
    final label = status.replaceAll('_', ' ');
    final color = switch (status.toLowerCase()) {
      'verified' => const Color(0xFF007A3F),
      'needs_correction' => const Color(0xFFB42318),
      _ => const Color(0xFF8A6700),
    };
    return Text(
      label,
      style: TextStyle(color: color, fontWeight: FontWeight.w600),
    );
  }
}

class _MessageBox extends StatelessWidget {
  const _MessageBox({required this.message, required this.onRetry});
  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      color: const Color(0xFFFFEEEE),
      borderRadius: BorderRadius.circular(8),
    ),
    child: Row(
      children: [
        Expanded(
          child: Text(
            message,
            style: const TextStyle(color: Color(0xFF8F1D1D)),
          ),
        ),
        IconButton(onPressed: onRetry, icon: const Icon(Icons.refresh)),
      ],
    ),
  );
}
