@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/student-sidebar.css',
        'resources/css/student/upload.css'
    ])
@endpush

@section('content')
<div class="student-layout">

    @include('components.student-sidebar')

    <main class="student-main-content">

        <div class="upload-container">

            <section class="upload-header">

                <h1>Upload Documents</h1>

                <p>Choose a document category and upload the required file.</p>
            
            </section>

            <form enctype="multipart/form-data">

                @csrf
                <section class="document-type-section">

                    <label for="documentType">Document Type</label>

                    <div class="document-type-wrapper">

                        <select name="document_type" required>
                            <option value="Birth Certificate">Birth Certificate</option>
                        </select>

                        <img src="{{ asset('icons/drop-down-light.svg') }}" alt="">
                    
                    </div>

                </section>

                <section class="file-upload-section">

                    <div class="upload-dropzone">

                        <img src="{{ asset('icons/upload-red-light.svg') }}" alt="Upload" class="upload-icon">

                        <h2>Drag and drop your files here</h2>

                        <p>or choose a file from your device</p>

                        <div class="browse-input-wrapper">

                            <span>Browse Files</span>

                            <input type="file" name="documents[]" accept=".pdf,application/pdf" multiple required>

                        </div>

                        <small>
                            PDF · Maximum 10 MB per file
                        </small>

                    </div>

                </section>

                <section class="selected-files-section">
                    <div class="selected-files">
                        {{-- Selected files will appear here through JavaScript --}}
                    </div>

                    <p id="uploadError" class="upload-error" role="alert"></p>
                </section>

                <div class="upload-submit-section">
                    <button type="submit">Submit Documents</button>
                </div>

            </form>

        </div>

    </main>

</div>
@endsection