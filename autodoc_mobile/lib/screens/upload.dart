import 'package:dotted_border/dotted_border.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';

import '../services/api_client.dart';

class Upload extends StatefulWidget {
  const Upload({super.key});

  @override
  State<Upload> createState() => _UploadState();
}

class _UploadState extends State<Upload> {
  static const _fallbackExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
  List<String> _categories = [];
  Set<String> _unavailableCategories = {};
  List<String> _extensions = _fallbackExtensions;
  List<PlatformFile> _files = [];
  String? _selectedCategory;
  String? _error;
  int _maxSizeMb = 10;
  bool _loadingRules = true;
  bool _uploading = false;

  @override
  void initState() {
    super.initState();
    _loadRules();
  }

  Future<void> _loadRules() async {
    try {
      final response = await ApiClient.instance.get('/mobile/document-types');
      final categories = List<String>.from(
        (response['document_types'] as List? ?? const []).map(
          (item) => item.toString(),
        ),
      );
      final unavailableCategories = Set<String>.from(
        (response['unavailable_document_types'] as List? ?? const []).map(
          (item) => item.toString(),
        ),
      );
      final availableCategories = categories
          .where((name) => !unavailableCategories.contains(name))
          .toList();
      final rules = response['upload_rules'] is Map
          ? Map<String, dynamic>.from(response['upload_rules'] as Map)
          : <String, dynamic>{};
      final formats = List<String>.from(
        (rules['accepted_formats'] as List? ?? _fallbackExtensions).map(
          (item) => item.toString().toLowerCase().replaceFirst('.', ''),
        ),
      );
      if (!mounted) return;
      setState(() {
        _categories = categories;
        _unavailableCategories = unavailableCategories;
        _selectedCategory = availableCategories.isEmpty
            ? null
            : availableCategories.first;
        _extensions = formats.isEmpty ? _fallbackExtensions : formats;
        _maxSizeMb =
            int.tryParse(rules['max_file_size_mb']?.toString() ?? '') ?? 10;
      });
    } catch (error) {
      if (mounted) setState(() => _error = error.toString());
    } finally {
      if (mounted) setState(() => _loadingRules = false);
    }
  }

  Future<void> _pickFiles() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: _extensions,
      allowMultiple: false,
      withData: true,
    );
    if (!mounted || result == null) return;
    if (result.files.any((file) => file.size > _maxSizeMb * 1024 * 1024)) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Each file must be no larger than $_maxSizeMb MB.'),
        ),
      );
      return;
    }
    setState(() {
      _files = result.files.take(1).toList();
      _error = null;
    });
  }

  Future<void> _submit() async {
    final category = _selectedCategory;
    if (category == null || _files.isEmpty || _uploading) return;
    setState(() {
      _uploading = true;
      _error = null;
    });
    try {
      final response = await ApiClient.instance.uploadDocuments(
        documentType: category,
        files: _files,
      );
      if (!mounted) return;
      final message =
          response['message']?.toString() ??
          'Successfully uploaded, wait for your status';
      setState(() => _files = []);
      await showDialog<void>(
        context: context,
        builder: (dialogContext) => AlertDialog(
          insetPadding: const EdgeInsets.symmetric(
            horizontal: 24,
            vertical: 24,
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          icon: const Icon(
            Icons.check_circle_outline,
            color: Color(0xFF16834A),
            size: 42,
          ),
          title: const Text('Upload successful', textAlign: TextAlign.center),
          content: Text(message, textAlign: TextAlign.center),
          actionsAlignment: MainAxisAlignment.center,
          actions: [
            TextButton(
              onPressed: () => Navigator.of(dialogContext).pop(),
              child: const Text('OK'),
            ),
          ],
        ),
      );
      await _loadRules();
    } catch (error) {
      if (mounted) setState(() => _error = error.toString());
    } finally {
      if (mounted) setState(() => _uploading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final extensionsLabel = _extensions
        .map((value) => value.toUpperCase())
        .join(' · ');
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: SafeArea(
        child: _loadingRules
            ? const Center(child: CircularProgressIndicator())
            : RefreshIndicator(
                onRefresh: _loadRules,
                child: ListView(
                  padding: const EdgeInsets.all(15),
                  children: [
                    const Text(
                      'Upload Documents',
                      style: TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Choose a document category and upload one file.',
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'Capture or scan the document clearly, with all text and signatures visible, to avoid incorrect data or an invalid submission.',
                      style: TextStyle(color: Color(0xFF7A1F1F)),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Only one file can be submitted per document type. If it needs correction, you can upload a replacement.',
                    ),
                    if (_error != null) ...[
                      const SizedBox(height: 12),
                      _ErrorMessage(_error!),
                    ],
                    const SizedBox(height: 16),
                    if (_categories.isEmpty)
                      const Text(
                        'No document types are available. Contact an administrator.',
                      )
                    else ...[
                      if (_selectedCategory == null)
                        const Padding(
                          padding: EdgeInsets.only(bottom: 8),
                          child: Text(
                            'All document types have already been submitted or are awaiting review.',
                          ),
                        ),
                      DropdownButtonFormField<String>(
                        initialValue: _selectedCategory,
                        decoration: InputDecoration(
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        items: _categories.map((name) {
                          final unavailable = _unavailableCategories.contains(
                            name,
                          );
                          return DropdownMenuItem(
                            value: name,
                            enabled: !unavailable,
                            child: Text(
                              unavailable ? '$name — Already submitted' : name,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          );
                        }).toList(),
                        onChanged: _uploading
                            ? null
                            : (value) =>
                                  setState(() => _selectedCategory = value),
                      ),
                    ],
                    const SizedBox(height: 15),
                    DottedBorder(
                      color: const Color(0xFF951F1C),
                      strokeWidth: 1.5,
                      dashPattern: const [6, 3],
                      borderType: BorderType.RRect,
                      radius: const Radius.circular(8),
                      child: SizedBox(
                        width: double.infinity,
                        height: 235,
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(
                              Icons.file_upload_outlined,
                              color: Color(0xFF951F1C),
                              size: 60,
                            ),
                            const SizedBox(height: 12),
                            const Text('Choose files from your device'),
                            const SizedBox(height: 14),
                            ElevatedButton(
                              onPressed: _uploading ? null : _pickFiles,
                              child: const Text('Browse Files'),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              '$extensionsLabel · Maximum $_maxSizeMb MB per file',
                            ),
                          ],
                        ),
                      ),
                    ),
                    for (var index = 0; index < _files.length; index++)
                      ListTile(
                        contentPadding: EdgeInsets.zero,
                        title: Text(
                          _files[index].name,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        subtitle: Text(
                          '${(_files[index].size / (1024 * 1024)).toStringAsFixed(1)} MB',
                        ),
                        trailing: IconButton(
                          onPressed: _uploading
                              ? null
                              : () => setState(() => _files.removeAt(index)),
                          icon: const Icon(Icons.close),
                        ),
                      ),
                    const SizedBox(height: 12),
                    SizedBox(
                      height: 50,
                      child: ElevatedButton(
                        onPressed:
                            _files.isEmpty ||
                                _selectedCategory == null ||
                                _uploading
                            ? null
                            : _submit,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFD32F2F),
                          foregroundColor: Colors.white,
                        ),
                        child: _uploading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: Colors.white,
                                ),
                              )
                            : const Text('Submit Documents'),
                      ),
                    ),
                  ],
                ),
              ),
      ),
    );
  }
}

class _ErrorMessage extends StatelessWidget {
  const _ErrorMessage(this.message);
  final String message;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      color: Theme.of(context).colorScheme.errorContainer,
      borderRadius: BorderRadius.circular(8),
    ),
    child: Text(
      message,
      style: TextStyle(color: Theme.of(context).colorScheme.onErrorContainer),
    ),
  );
}
