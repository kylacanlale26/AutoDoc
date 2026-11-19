
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/review.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="review-container">

            <section class="review-header">
                <a class="back-to-verification" href="{{ route('admin.verification') }}">← Back to Document Verification</a>
                <h1>Review Submission</h1>
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

            <section class="review-content">

                <div class="document-review-panel">

                    <div class="document-review-header">

                        <div class="document-info">

                            <h2>{{ $document->document_name }}</h2>

                            <p>{{ $document->student->name }} · {{ number_format((int) $document->file_size_bytes / 1048576, 2) }} MB</p>

                        </div>

                        <span class="document-status {{ strtolower(str_replace(' ', '-', $document->status)) }}">
                            {{ str_replace('_', ' ', ucfirst($document->status)) }}
                        </span>

                    </div>

                    <div class="document-preview-placeholder">
                        <iframe
                            class="review-document-iframe"
                            src="{{ route('admin.documents.preview', $document) }}"
                            title="Preview of {{ $document->document_name }}"
                        ></iframe>
                    </div>

                    <a class="open-full-preview" href="{{ route('admin.documents.preview', $document) }}" target="_blank" rel="noopener">
                        Open Full Preview
                    </a>

                </div>

                <div class="verification-review-panel">

                    @php
                        $analysis = $document->analysis;
                        $analysisResult = $analysis?->result ?? [];
                        $classification = $analysisResult['documentClassification'] ?? [];
                        $extraction = $analysisResult['extraction'] ?? [];
                        $verification = $analysisResult['verification'] ?? [];
                        $signatures = $extraction['signatures'] ?? [];
                    @endphp

                    <section class="document-analysis-panel" aria-labelledby="document-analysis-title">
                        <h2 id="document-analysis-title">Document analysis</h2>
                        @if ($analysis)
                            <p class="analysis-status">Analysis: {{ str_replace('_', ' ', ucfirst($analysis->status)) }}</p>

                            @if ($analysis->status === 'processing')
                                <p>AI analysis is still running. This page will check again automatically.</p>
                                @push('scripts')
                                    <script>
                                        (() => {
                                            const key = 'autodoc-analysis-polls-{{ $document->id }}';
                                            const attempts = Number(sessionStorage.getItem(key) || 0);
                                            if (attempts < 24) {
                                                sessionStorage.setItem(key, String(attempts + 1));
                                                window.setTimeout(() => window.location.reload(), 5000);
                                            } else {
                                                sessionStorage.removeItem(key);
                                            }
                                        })();
                                    </script>
                                @endpush
                            @else
                                @push('scripts')
                                    <script>sessionStorage.removeItem('autodoc-analysis-polls-{{ $document->id }}');</script>
                                @endpush
                            @endif

                            @if (!empty($analysisResult['error']))
                                <p role="status">
                                    <strong>Analysis error:</strong> {{ $analysisResult['error'] }}
                                    Please review the uploaded file manually. If the error says the model is busy, retry the analysis later.
                                </p>
                            @else
                                @if (!empty($classification['summary']))
                                    <p>{{ $classification['summary'] }}</p>
                                @endif

                                @if (!empty($verification['verdictSummary']))
                                    <p><strong>AI review suggestion:</strong> {{ $verification['verdictSummary'] }}</p>
                                @endif

                                @if (!empty($verification['checks']))
                                    <h3>Checks</h3>
                                    <ul>
                                        @foreach ($verification['checks'] as $check)
                                            <li>
                                                <strong>{{ $check['checkName'] ?? 'Check' }}</strong>
                                                ({{ str_replace('_', ' ', $check['status'] ?? 'inconclusive') }}):
                                                {{ $check['detail'] ?? '' }}
                                                @if (!empty($check['evidence']))
                                                    <span> Evidence: {{ $check['evidence'] }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <h3>Signatures ({{ count($signatures) }})</h3>
                                @if ($signatures)
                                    <ul>
                                        @foreach ($signatures as $signature)
                                            <li>
                                                {{ $signature['signerName'] ?? 'Signer not identified' }}
                                                @if (!empty($signature['roleOrLabel'])) · {{ $signature['roleOrLabel'] }} @endif
                                                @if (!empty($signature['signedDate'])) · {{ $signature['signedDate'] }} @endif
                                                @if (isset($signature['matchesPrintedName']))
                                                    · Name check: {{ str_replace('_', ' ', $signature['matchesPrintedName']) }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No signatures were reported by the analysis.</p>
                                @endif

                                @if (!empty($extraction['rawText']))
                                    <details>
                                        <summary>View extracted text</summary>
                                        <pre>{{ $extraction['rawText'] }}</pre>
                                    </details>
                                @endif
                            @endif
                        @else
                            <p>No analysis is available for this document.</p>
                        @endif
                        <p class="analysis-disclaimer">AI results are suggestions for review. Make the final decision by checking the original document.</p>
                    </section>

                    <form method="POST" action="{{ route('admin.documents.status.update', $document) }}">
                        @csrf
                    <section class="administrator-notes">

                        <label for="admin-notes">
                            Administrator Notes
                        </label>

                        <textarea
                            id="admin-notes"
                            name="review_notes"
                            placeholder="Type the reason for your decision..."
                        >{{ old('review_notes', $document->review_notes) }}</textarea>

                    </section>

                    <div class="review-actions">

                        <button
                            type="submit"
                            name="status"
                            value="verified"
                            class="approve-document"
                        >
                            Approve Document
                        </button>

                        <button
                            type="submit"
                            name="status"
                            value="needs_correction"
                            class="request-correction"
                        >
                            Request Correction
                        </button>

                    </div>
                    </form>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection
