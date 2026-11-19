import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

import '../services/api_client.dart';
import '../widgets/autodoc_loading.dart';

class DocumentDetail extends StatefulWidget {
  const DocumentDetail({
    super.key,
    required this.documentId,
    this.notificationMessage,
  });
  final String documentId;
  final String? notificationMessage;
  @override
  State<DocumentDetail> createState() => _DocumentDetailState();
}

class _DocumentDetailState extends State<DocumentDetail> {
  Map<String, dynamic> document = {};
  bool loading = true;
  String? error;
  String? imagePreviewUrl;
  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final result = await ApiClient.instance.get(
        '/mobile/documents/${widget.documentId}',
      );
      document = Map<String, dynamic>.from(result['document'] as Map? ?? {});
      if ((document['mime_type']?.toString() ?? '').startsWith('image/')) {
        final file = await ApiClient.instance.get(
          '/mobile/documents/${widget.documentId}/file?disposition=inline',
        );
        imagePreviewUrl = file['url']?.toString();
      }
    } catch (e) {
      error = e.toString();
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  Future<void> _openFile(String disposition) async {
    try {
      final result = await ApiClient.instance.get(
        '/mobile/documents/${widget.documentId}/file?disposition=$disposition',
      );
      final uri = Uri.parse(result['url'].toString());
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        throw const ApiException('Could not open this file.');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(e.toString())));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final type = document['mime_type']?.toString() ?? '';
    final preview = imagePreviewUrl ?? '';
    final status =
        document['status']?.toString().replaceAll('_', ' ') ?? 'pending';
    return Scaffold(
      appBar: loading
          ? null
          : AppBar(
        title: const Text('Document Details'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('My Documents'),
          ),
        ],
            ),
      body: loading
          ? const AutoDocLoading()
          : error != null
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text(error!, textAlign: TextAlign.center),
              ),
            )
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Text(
                  document['document_type']?.toString() ?? 'Document',
                  style: Theme.of(context).textTheme.headlineSmall
                      ?.copyWith(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 4),
                Text(
                  document['document_name']?.toString() ?? '',
                  style: Theme.of(context).textTheme.titleMedium,
                ),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: scheme.surfaceContainerHighest,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      const Text(
                        'Status',
                        style: TextStyle(fontWeight: FontWeight.bold),
                      ),
                      const Spacer(),
                      Text(
                        status.toUpperCase(),
                        style: TextStyle(
                          color: _statusColor(status),
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
                if (widget.notificationMessage?.isNotEmpty == true)
                  _note(context, 'Notification', widget.notificationMessage!),
                if (document['admin_recommendation']?.toString().isNotEmpty ==
                    true)
                  _note(
                    context,
                    'Admin recommendation',
                    document['admin_recommendation'].toString(),
                  ),
                if (document['analysis_note']?.toString().isNotEmpty == true)
                  _note(
                    context,
                    'Automated review',
                    document['analysis_note'].toString(),
                  ),
                const SizedBox(height: 12),
                if (type.startsWith('image/') && preview.isNotEmpty)
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: SizedBox(
                      width: double.infinity,
                      height: 400,
                      child: Image.network(
                        preview,
                        width: double.infinity,
                        height: 400,
                        fit: BoxFit.contain,
                        loadingBuilder: (c, child, progress) => progress == null
                            ? child
                            : const SizedBox(
                                height: 250,
                                child: Center(
                                  child: CircularProgressIndicator(),
                                ),
                              ),
                        errorBuilder: (_, _, _) => const SizedBox(
                          height: 180,
                          child: Center(child: Text('Preview unavailable')),
                        ),
                      ),
                    ),
                  )
                else
                  Container(
                    height: 220,
                    decoration: BoxDecoration(
                      color: scheme.surfaceContainerHighest,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.picture_as_pdf_outlined,
                            size: 54,
                            color: scheme.primary,
                          ),
                          const SizedBox(height: 8),
                          const Text('PDF document'),
                          const Text(
                            'Open the file to preview the full document',
                          ),
                        ],
                      ),
                    ),
                  ),
                const SizedBox(height: 14),
                if (!type.startsWith('image/'))
                  OutlinedButton.icon(
                    onPressed: () => _openFile('inline'),
                    icon: const Icon(Icons.open_in_new),
                    label: const Text('Open Full Preview'),
                  ),
                FilledButton.icon(
                  onPressed: () => _openFile('attachment'),
                  icon: const Icon(Icons.download),
                  label: const Text('Download / Export File'),
                ),
              ],
            ),
    );
  }

  Widget _note(BuildContext context, String title, String body) => Container(
    margin: const EdgeInsets.only(top: 12),
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(
      color: Theme.of(context).colorScheme.surfaceContainerHighest,
      borderRadius: BorderRadius.circular(12),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(height: 5),
        Text(body),
      ],
    ),
  );
  Color _statusColor(String status) => status.toLowerCase().contains('verified')
      ? const Color(0xFF16834A)
      : status.toLowerCase().contains('correction')
      ? const Color(0xFFD32F2F)
      : const Color(0xFFB18400);
}
