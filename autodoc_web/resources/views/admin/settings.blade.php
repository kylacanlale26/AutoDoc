
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/settings.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="settings-container">

            <section class="settings-header">

                <h1>Settings</h1>

                <p>Manage document types and upload rules.</p>

            </section>

            <section class="document-types-section">

                <h2>Document Types</h2>

                <div class="document-types-list">

                    @forelse (($documentTypes ?? [ 'Birth Certificate' ]) as $documentType)

                        <div class="document-type-item">

                            <span class="document-type-name">
                                {{ is_string($documentType) ? $documentType : $documentType->name }}
                            </span>

                            <button type="button" class="edit-document-type" aria-label="Edit document type">

                                <img src="{{ asset('icons/edit-light.svg') }}" alt="">

                            </button>

                        </div>

                    @empty
                        <div class="empty-document-types">
                            No Document Types Available
                        </div>

                    @endforelse

                </div>

                <div class="add-document-type">

                    <input type="text" name="new_document_type" placeholder="Add document type..." aria-label="New document type">

                    <button type="button" class="add-document-button" >
                        + Add
                    </button>

                </div>

            </section>

            <section class="upload-rules-section">

                <h2>Upload Rules</h2>

                <div class="upload-rule-item">

                    <div class="upload-rule-info">

                        <h3>Maximum file size</h3>

                        <p>Limit per uploaded file</p>

                    </div>

                    <input type="text" name="max_file_size" value="10 MB" class="upload-rule-input" aria-label="Maximum file size">

                </div>

                <div class="upload-rule-item">

                    <div class="upload-rule-info">

                        <h3>Accepted formats</h3>

                        <p>Allowed file extensions</p>

                    </div>

                    <input type="text" name="accepted_formats" value="PDF" class="upload-rule-input" aria-label="Accepted file formats">

                </div>

            </section>

            <button type="button" class="save-settings-button">
                Save Changes
            </button>

        </div>

    </main>

</div>

@endsection
