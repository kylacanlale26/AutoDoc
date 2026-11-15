
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/settings.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/admin/settings.js')
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="settings-container">

            <section class="settings-header">

                <h1>Settings</h1>

                <p>Manage document types and upload rules.</p>

                @if (session('success'))
                    <p role="status">{{ session('success') }}</p>
                @endif

                @if ($errors->any())
                    <div role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

            </section>

            <section class="document-types-section">

                <h2>Document Types</h2>

                <div class="document-types-list">

                    @forelse (($documentTypes ?? []) as $documentType)

                        <div class="document-type-item">

                            <span class="document-type-name">
                                {{ $documentType->name }}
                            </span>

                            <button
                                type="button"
                                class="edit-document-type"
                                data-edit-type="edit-document-type-{{ $documentType->id }}"
                                aria-label="Edit {{ $documentType->name }}"
                                aria-expanded="false"
                                aria-controls="edit-document-type-{{ $documentType->id }}"
                            >

                                <img src="{{ asset('icons/edit-light.svg') }}" alt="">

                            </button>

                            <div class="document-type-edit-panel" id="edit-document-type-{{ $documentType->id }}" hidden>
                                <form method="POST" action="{{ route('admin.settings.document-types.update', $documentType) }}" class="document-type-edit-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="document_type_id" value="{{ $documentType->id }}">
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ (string) old('document_type_id') === (string) $documentType->id ? old('name', $documentType->name) : $documentType->name }}"
                                        maxlength="120"
                                        required
                                        aria-label="Rename {{ $documentType->name }}"
                                    >
                                    <button type="submit" class="add-document-button">Save Name</button>
                                </form>

                                <form method="POST" action="{{ route('admin.settings.document-types.delete', $documentType) }}" onsubmit="return confirm('Delete this document type? Students will no longer be able to select it.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-document-type">Delete</button>
                                </form>
                            </div>

                        </div>

                    @empty
                        <div class="empty-document-types">
                            No Document Types Available
                        </div>

                    @endforelse

                </div>

                <form method="POST" action="{{ route('admin.settings.document-types.store') }}" class="add-document-type">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}" maxlength="120" placeholder="Add document type..." aria-label="New document type" required>

                    <button type="submit" class="add-document-button">
                        + Add
                    </button>
                </form>

            </section>

            @php
                $acceptedFormats = $uploadSettings->accepted_formats ?? ['pdf', 'png', 'jpg', 'jpeg'];
                $displayFormats = array_values(array_unique(array_map(
                    fn ($format) => in_array(strtolower($format), ['jpg', 'jpeg'], true) ? 'JPEG' : strtoupper($format),
                    $acceptedFormats
                )));
            @endphp

            <form method="POST" action="{{ route('admin.settings.upload-rules.update') }}">
                @csrf
                @method('PUT')
            <section class="upload-rules-section">

                <h2>Upload Rules</h2>

                <div class="upload-rule-item">

                    <div class="upload-rule-info">

                        <h3>Maximum file size</h3>

                        <p>Limit per uploaded file</p>

                    </div>

                    <div class="upload-rule-input" aria-label="Maximum file size">{{ $uploadSettings->max_file_size_mb }} MB</div>

                </div>

                <div class="upload-rule-item">

                    <div class="upload-rule-info">

                        <h3>Accepted formats</h3>

                        <p>Allowed file extensions</p>

                    </div>

                    <div class="upload-rule-input" aria-label="Accepted file formats">{{ implode(', ', $displayFormats) }}</div>

                </div>

            </section>

            <button type="submit" class="save-settings-button">
                Save Changes
            </button>
            </form>

        </div>

    </main>

</div>

@endsection
