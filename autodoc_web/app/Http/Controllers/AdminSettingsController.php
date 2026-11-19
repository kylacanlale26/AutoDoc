<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\UploadSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $documentTypes = DocumentType::query()->orderBy('name')->get();
        $uploadSettings = UploadSetting::current();

        return view('admin.settings', compact('documentTypes', 'uploadSettings'));
    }

    public function storeDocumentType(Request $request): RedirectResponse
    {
        $this->ensureAdmin();
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('document_types', 'name')],
        ]);

        DocumentType::create(['name' => trim($validated['name'])]);

        return redirect()->route('admin.settings')->with('success', 'Document type added.');
    }

    public function updateDocumentType(Request $request, DocumentType $documentType): RedirectResponse
    {
        $this->ensureAdmin();
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('document_types', 'name')->ignore($documentType->id),
            ],
        ]);

        $documentType->update(['name' => trim($validated['name'])]);

        return redirect()->route('admin.settings')->with('success', 'Document type renamed.');
    }

    public function deleteDocumentType(DocumentType $documentType): RedirectResponse
    {
        $this->ensureAdmin();

        $documentType->delete();

        return redirect()->route('admin.settings')->with('success', 'Document type deleted.');
    }

    public function saveFixedUploadRules(): RedirectResponse
    {
        $this->ensureAdmin();

        UploadSetting::current()->update([
            'max_file_size_mb' => 10,
            'accepted_formats' => ['pdf', 'png', 'jpg', 'jpeg'],
        ]);

        return redirect()->route('admin.settings')->with('success', 'Upload rules saved.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(strtolower((string) auth()->user()->account_role) === 'admin', 403);
    }
}
