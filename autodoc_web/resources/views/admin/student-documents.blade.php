
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/student-documents.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('components.admin-sidebar')

    <main class="admin-main-content">

        <div class="student-documents-container">

            <section class="student-documents-header">
                <h1>Student Documents</h1>
            </section>

            <section class="student-search-section">

                <div class="student-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input type="text" placeholder="Search student">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="student-documents-list">

                @forelse ($students ?? [] as $student)

                    <div class="student-document-item">

                        <div class="student-document-info">

                            <h2>
                                {{ $student->name }}
                            </h2>

                        </div>

                        <div class="student-document-action">
                            <a href="{{ route('admin.student-docs') }}">
                            <img src="{{ asset('icons/arrow-right-light.svg') }}" alt="View student documents">
                        </div>

                    </div>

                @empty
                    <div class="empty-student-documents">
                        No Student
                    </div>

                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
