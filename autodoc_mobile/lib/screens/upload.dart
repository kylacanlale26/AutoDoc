import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'package:dotted_border/dotted_border.dart';

class Upload extends StatefulWidget {
  const Upload({super.key});

  @override
  State<Upload> createState() => _UploadState();
}

class _UploadState extends State<Upload> {
  String selectedCategory = 'Birth Certificate';
  PlatformFile? selectedFile;

  final List<String> categories = [
    'Birth Certificate',
  ];

  Future<void> pickFile() async {
    FilePickerResult? result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf'],
    );

    if (result != null) {
      PlatformFile file = result.files.first;

      if (file.size > 10 * 1024 * 1024) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('File must not exceed 10 MB.'),
          ),
        );
        return;
      }

      setState(() {
        selectedFile = file;
      });
    }
  }

  void removeFile() {
    setState(() {
      selectedFile = null;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 15),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 12),

                    const Text(
                      'Upload Documents',
                      style: TextStyle(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    const Text(
                      'Choose a document category and upload the required file.',
                      style: TextStyle(
                        fontSize: 14,
                        letterSpacing: 1,
                      ),
                    ),

                    const SizedBox(height: 14),

                    DropdownButtonFormField<String>(
                      value: selectedCategory,
                      decoration: InputDecoration(
                        contentPadding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 3,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      icon: const Icon(
                        Icons.keyboard_arrow_down,
                        color: Colors.black,
                        size: 35,
                      ),
                      items: categories.map((category) {
                        return DropdownMenuItem(
                          value: category,
                          child: Text(
                            category,
                            style: const TextStyle(fontSize: 14),
                          ),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedCategory = value!;
                        });
                      },
                    ),

                    const SizedBox(height: 15),

                    DottedBorder(
                      color: const Color(0xFF951F1C),
                      strokeWidth: 1.5,
                      dashPattern: const [6, 3],
                      borderType: BorderType.RRect,
                      radius: const Radius.circular(8),
                      child: Container(
                        width: double.infinity,
                        height: 300,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 15,
                          vertical: 25,
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(
                              Icons.file_upload_outlined,
                              color: Color(0xFF951F1C),
                              size: 70,
                            ),

                            const SizedBox(height: 15),

                            const Text(
                              'Choose a file from your device',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontSize: 17,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 1,
                              ),
                            ),

                            const SizedBox(height: 22),

                            SizedBox(
                              width: 192,
                              height: 46,
                              child: ElevatedButton(
                                onPressed: pickFile,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFFD32F2F),
                                  foregroundColor: Colors.white,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(7),
                                  ),
                                ),
                                child: const Text(
                                  'Browse Files',
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ),

                            const SizedBox(height: 12),

                            const Text(
                              'PDF · Maximum 10 MB per file',
                              style: TextStyle(
                                fontSize: 13,
                                letterSpacing: 1,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),

                    if (selectedFile != null) ...[
                      const SizedBox(height: 15),

                      Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  selectedFile!.name,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                    letterSpacing: 1,
                                  ),
                                ),

                                Text(
                                  '${(selectedFile!.size / (1024 * 1024)).toStringAsFixed(1)} MB · Ready to upload',
                                  style: const TextStyle(
                                    fontSize: 10,
                                    letterSpacing: 1,
                                  ),
                                ),
                              ],
                            ),
                          ),

                          IconButton(
                            onPressed: removeFile,
                            icon: const Icon(
                              Icons.close,
                              color: Colors.black,
                              size: 28,
                            ),
                          ),
                        ],
                      ),
                    ],

                    const SizedBox(height: 15),

                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: selectedFile == null
                            ? null
                            : () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text(
                                      'Ready to submit your document.',
                                    ),
                                  ),
                                );
                              },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFD32F2F),
                          foregroundColor: Colors.white,
                          disabledBackgroundColor: const Color(0xFFD32F2F),
                          disabledForegroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(7),
                          ),
                        ),
                        child: const Text(
                          'Submit Documents',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}