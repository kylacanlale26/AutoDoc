@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/upload.css'
    ])
@endpush

@section('content')
@php
    $acceptedFormats = ['pdf', 'png', 'jpg', 'jpeg'];
    $acceptExtensions = implode(',', array_map(fn ($format) => '.' . $format, $acceptedFormats));
    $acceptedFormatLabels = array_values(array_unique(array_map(
        fn ($format) => in_array(strtolower($format), ['jpg', 'jpeg'], true) ? 'JPEG' : strtoupper($format),
        $acceptedFormats
    )));
    $acceptedFormatsLabel = implode(', ', $acceptedFormatLabels);
@endphp
<div class="student-layout">

    @include('sidebar.student-sidebar')

    <main class="student-main-content">

        <div class="upload-container">

            <section class="upload-header">

                <h1>Upload Documents</h1>

                <p>Choose a document category and upload the required file.</p>
                <p class="upload-capture-note">Capture or scan each document clearly, with all text and signatures visible, to avoid incorrect data or an invalid submission.</p>
                <p class="upload-limit-note">Upload one file per document type. If it needs correction, you can upload a replacement.</p>
            
            </section>

            @if (session('success'))
                <dialog id="uploadSuccessDialog" class="upload-success-dialog" aria-labelledby="uploadSuccessTitle">
                    <div class="upload-success-content">
                        <span class="upload-success-icon" aria-hidden="true">✓</span>
                        <h2 id="uploadSuccessTitle">Upload successful</h2>
                        <p role="status">{{ session('success') }}</p>
                        <button type="button" id="closeUploadSuccess">OK</button>
                    </div>
                </dialog>
            @endif

            @if ($errors->any())
                <div role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.upload.store') }}" enctype="multipart/form-data">

                @csrf
                <section class="document-type-section">

                    <label for="documentType">Document Type</label>

                    <div class="document-type-wrapper">

                        <select name="document_type" id="documentType" required>
                            <option value="" disabled @selected(old('document_type') === null)>Select document type</option>
                            @foreach ($documentTypes ?? [] as $documentType)
                                @php
                                    $documentTypeUnavailable = in_array($documentType->name, $unavailableDocumentTypes ?? [], true);
                                @endphp
                                <option value="{{ $documentType->name }}" @if ($documentTypeUnavailable) disabled @endif @if (old('document_type') === $documentType->name) selected @endif>
                                    {{ $documentType->name }}{{ $documentTypeUnavailable ? ' — Already submitted' : '' }}
                                </option>
                            @endforeach
                        </select>

                        <img src="{{ asset('icons/drop-down-light.svg') }}" alt="">
                    
                    </div>

                </section>

                <section class="file-upload-section">

                    <div class="upload-dropzone">

                        <img src="{{ asset('icons/upload-red-light.svg') }}" alt="Upload" class="upload-icon">

                        <h2>Drag and drop your files here</h2>

                        <p>or choose a file from your device</p>

                        <div class="browse-file-control">
                            <input
                                id="documentFiles"
                                class="visually-hidden-file-input"
                                type="file"
                                name="documents[]"
                                accept="{{ $acceptExtensions }}"
                                required
                                aria-describedby="uploadError"
                            >
                            <label for="documentFiles" class="browse-input-wrapper">
                                <span>Browse One File</span>
                            </label>
                        </div>

                        <small>
                            {{ $acceptedFormatsLabel }} · Maximum {{ $uploadSettings->max_file_size_mb }} MB per file
                        </small>

                    </div>

                </section>

                <section class="selected-files-section">
                    <div class="selected-files">
                        {{-- Selected files will appear here through JavaScript --}}
                    </div>

                    <p id="uploadError" class="upload-error" role="alert" aria-live="polite"></p>
                </section>

                <div class="upload-submit-section">
                    <button type="submit">Submit Documents</button>
                </div>

            </form>

        </div>

    </main>

</div>
@endsection

@php
    $uploadRulesForJs = [
        'maxFileSizeBytes' => $uploadSettings->max_file_size_mb * 1024 * 1024,
        'maxFileSizeMb' => $uploadSettings->max_file_size_mb,
        'allowedFormats' => $acceptedFormats,
        'allowedFormatLabels' => $acceptedFormatLabels,
    ];
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const successDialog = document.getElementById('uploadSuccessDialog');
    if (successDialog instanceof HTMLDialogElement && !successDialog.open) {
        successDialog.showModal();
        document.getElementById('closeUploadSuccess')?.addEventListener('click', () => {
            successDialog.close();
        });
    }

    const input = document.getElementById('documentFiles');
    const dropzone = document.querySelector('.upload-dropzone');
    const filesList = document.querySelector('.selected-files');
    const errorMessage = document.getElementById('uploadError');
    if (!input || !dropzone || !filesList || !errorMessage) return;

    const uploadRules = @json($uploadRulesForJs);
    const maxFileSize = uploadRules.maxFileSizeBytes;
    const allowedFormats = uploadRules.allowedFormats.map((format) => format.toLowerCase());
    let selectedFiles = [];

    const syncInput = () => {
        const transfer = new DataTransfer();
        selectedFiles.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    };

    const renderFiles = () => {
        filesList.replaceChildren();
        selectedFiles.forEach((file, index) => {
            const row = document.createElement('div');
            row.className = 'selected-file';
            const details = document.createElement('div');
            details.className = 'selected-file-details';
            const name = document.createElement('span');
            name.className = 'selected-file-name';
            name.textContent = file.name;
            const size = document.createElement('span');
            size.className = 'selected-file-size';
            size.textContent = file.size < 1024 * 1024
                ? `${Math.max(1, Math.round(file.size / 1024))} KB`
                : `${(file.size / (1024 * 1024)).toFixed(1)} MB`;
            details.append(name, size);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'remove-file';
            remove.setAttribute('aria-label', `Remove ${file.name}`);
            remove.textContent = '�';
            remove.addEventListener('click', () => {
                selectedFiles = selectedFiles.filter((_, i) => i !== index);
                syncInput();
                renderFiles();
            });

            row.append(details, remove);
            filesList.append(row);
        });
    };

    const acceptFiles = (files) => {
        const incomingFiles = Array.from(files);
        if (incomingFiles.length > 1) {
            selectedFiles = [];
            syncInput();
            renderFiles();
            errorMessage.textContent = 'Choose only one file for this document type.';
            return;
        }

        const file = incomingFiles[0];
        const extension = file?.name.split('.').pop()?.toLowerCase() ?? '';
        const hasAllowedFormat = allowedFormats.includes(extension);
        const valid = file && hasAllowedFormat && file.size <= maxFileSize;
        selectedFiles = valid ? [file] : [];
        syncInput();
        errorMessage.textContent = file && !valid
            ? `Choose a ${uploadRules.allowedFormatLabels.join(', ')} file no larger than ${uploadRules.maxFileSizeMb} MB.`
            : '';
        renderFiles();
    };

    input.addEventListener('change', () => acceptFiles(Array.from(input.files || [])));

    ['dragenter', 'dragover'].forEach((name) => dropzone.addEventListener(name, (event) => {
        event.preventDefault();
        dropzone.classList.add('drag-over');
    }));
    ['dragleave', 'dragend'].forEach((name) => dropzone.addEventListener(name, () => {
        dropzone.classList.remove('drag-over');
    }));
    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropzone.classList.remove('drag-over');
        acceptFiles(Array.from(event.dataTransfer?.files || []));
    });
});
</script>
@endpush

