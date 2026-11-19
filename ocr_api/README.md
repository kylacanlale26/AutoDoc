# AutoDoc document analysis API

This is the Express API adapted from `kylacanlale26/ocr-with-signature-recognition`. It accepts a PDF, PNG, or JPEG at `POST /api/v1/ocr/process`, uses the Google Gemini API to extract document text and signature details, and returns structured JSON to Laravel. Laravel stores the analysis and keeps the document pending for an administrator to review.

## Run locally on Windows

1. Copy `.env.example` to `.env`.
2. Set `GEMINI_API_KEY` to an API key from Google AI Studio. Generate a long random `OCR_API_TOKEN` and put the same token in `autodoc_web/.env`. Set `LARAVEL_CALLBACK_URL` to the local Laravel callback address shown in `.env.example` (update the port if Laravel uses a different one).
3. Use Node.js 24 or newer. From this directory run `npm install`, then `npm start`.
4. Check `http://127.0.0.1:3000/api/v1/health`.

The server binds to `127.0.0.1` by default. For a private Docker or server deployment, set `HOST=0.0.0.0` and restrict access with a firewall or private network. All `/api/v1/ocr/*` routes require `Authorization: Bearer <OCR_API_TOKEN>`; the health endpoint is public.

## API behavior

- Accepted multipart field: `file` (PDF, PNG, or JPEG; maximum 20 MB).
- Optional fields: `mode`, `language`, `includeSignatures`, `customFields`, and `verificationRules`.
- Laravel submits to `POST /api/v1/ocr/jobs`, which returns immediately with a job ID while Gemini processes the document. On completion, the API posts the result to the fixed local Laravel callback endpoint, which updates the document status and notifies the student.
- The API accepts callback URLs only from `LARAVEL_CALLBACK_URL` and requires the target host to be loopback, so requests cannot choose an arbitrary destination.
- OCR jobs are held in memory by this local service. If it restarts before a job finishes, that job is lost and the document must be reviewed manually or uploaded again.
- This service sends each uploaded document, the student's full name, and the student's number to Google Gemini for extraction and verification, as authorized for this AutoDoc integration.
- A `verified` Gemini verdict maps the AutoDoc document status to `verified`. `needs_review`, `flagged`, or failed analysis maps to `needs_correction` with the analysis reason saved. While OCR is processing, the document remains `pending`. These automated results are suggestions, and an administrator can still review or change the document status.
- Jobs and the sample audit ledger are in memory and reset when the service restarts. Laravel stores completed analysis alongside its document record.
