import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

import '../services/api_client.dart';
import '../widgets/autodoc_loading.dart';

class MyDocuments extends StatefulWidget {
  const MyDocuments({super.key, required this.onOpenDocument, this.onLoadingChanged});
  final Future<void> Function(String id, {String? message}) onOpenDocument;
  final ValueChanged<bool>? onLoadingChanged;
  @override
  State<MyDocuments> createState() => _MyDocumentsState();
}

class _MyDocumentsState extends State<MyDocuments> {
  final searchController = TextEditingController();
  List<Map<String, dynamic>> documents = [];
  String selectedFilter = 'All';
  bool loading = true, showFilters = false;
  String? error;
  static const filters = ['All', 'Verified', 'Pending', 'Needs Correction'];
  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    _notifyLoading(documents.isEmpty);
    try {
      final response = await ApiClient.instance.get('/mobile/documents');
      documents = List<Map<String, dynamic>>.from(
        (response['documents'] as List? ?? const []).whereType<Map>().map(
          (e) => Map<String, dynamic>.from(e),
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

  String _normal(String value) =>
      value.toLowerCase().replaceAll('_', ' ').replaceAll('-', ' ').trim();
  List<Map<String, dynamic>> get filtered {
    final q = searchController.text.trim().toLowerCase();
    return documents
        .where(
          (d) =>
              ((d['document_name']?.toString().toLowerCase().contains(q) ??
                      false) ||
                  (d['document_type']?.toString().toLowerCase().contains(q) ??
                      false)) &&
              (selectedFilter == 'All' ||
                  _normal(d['status']?.toString() ?? '') ==
                      _normal(selectedFilter)),
        )
        .toList();
  }

  Future<void> _openFile(Map<String, dynamic> d, String disposition) async {
    try {
      final response = await ApiClient.instance.get(
        '/mobile/documents/${d['id']}/file?disposition=$disposition',
      );
      final uri = Uri.tryParse(response['url']?.toString() ?? '');
      if (uri == null ||
          !await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        throw const ApiException('Could not open the document.');
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
    final items = filtered;
    if (loading && documents.isEmpty) {
      return const SafeArea(child: AutoDocLoading());
    }
    return SafeArea(
      child: RefreshIndicator(
        onRefresh: _load,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 15),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 12),
              Text(
                'My Documents',
                style: Theme.of(context).textTheme.headlineSmall
                    ?.copyWith(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 14),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: searchController,
                      onChanged: (_) => setState(() {}),
                      decoration: InputDecoration(
                        hintText: 'Search documents',
                        prefixIcon: const Icon(Icons.search),
                        contentPadding: const EdgeInsets.symmetric(vertical: 8),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),
                  IconButton(
                    tooltip: 'Show or hide filters',
                    onPressed: () => setState(() => showFilters = !showFilters),
                    icon: Icon(
                      showFilters
                          ? Icons.filter_alt
                          : Icons.filter_alt_outlined,
                    ),
                  ),
                ],
              ),
              if (showFilters)
                SizedBox(
                  height: 48,
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    children: filters
                        .map(
                          (f) => Padding(
                            padding: const EdgeInsets.only(right: 7),
                            child: ChoiceChip(
                              label: Text(f),
                              selected: selectedFilter == f,
                              selectedColor: const Color(0xFFD32F2F),
                              labelStyle: TextStyle(
                                color: selectedFilter == f
                                    ? Colors.white
                                    : scheme.onSurface,
                              ),
                              onSelected: (_) =>
                                  setState(() => selectedFilter = f),
                            ),
                          ),
                        )
                        .toList(),
                  ),
                ),
              if (!showFilters) const SizedBox(height: 10),
              if (error != null)
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  child: Text(error!, style: TextStyle(color: scheme.error)),
                ),
              Expanded(
                child: items.isEmpty
                    ? Center(
                        child: Text(
                          documents.isEmpty
                              ? 'No documents have been uploaded yet.'
                              : 'No documents match your search or filter.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: scheme.onSurface),
                        ),
                      )
                    : ListView.separated(
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.only(top: 10, bottom: 18),
                        itemCount: items.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 8),
                        itemBuilder: (context, index) {
                          final d = items[index];
                          final status = d['status']?.toString() ?? 'pending';
                          return Container(
                            decoration: BoxDecoration(
                              color: scheme.surface,
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
                                d['document_type']?.toString() ?? 'Document',
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              subtitle: Text(
                                '${d['document_name'] ?? ''}\n${_date(d['created_at'])} · ${status.replaceAll('_', ' ')}',
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                              isThreeLine: true,
                              onTap: () async {
                                await widget.onOpenDocument(d['id'].toString());
                                await _load();
                              },
                              trailing: PopupMenuButton<String>(
                                onSelected: (v) => _openFile(
                                  d,
                                  v == 'download' ? 'attachment' : 'inline',
                                ),
                                itemBuilder: (_) => const [
                                  PopupMenuItem(
                                    value: 'preview',
                                    child: Text('Preview File'),
                                  ),
                                  PopupMenuItem(
                                    value: 'download',
                                    child: Text('Download/Export File'),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _date(dynamic value) {
    final d = DateTime.tryParse(value?.toString() ?? '');
    if (d == null) return '';
    return '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';
  }
}
