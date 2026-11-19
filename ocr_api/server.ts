import 'dotenv/config';
import crypto from 'node:crypto';
import path from 'node:path';
import express from 'express';
import type { Request, Response, NextFunction } from 'express';
import multer from 'multer';
import { GoogleGenAI, ThinkingLevel, Type } from '@google/genai';
import type {
  LedgerEntry,
  OcrDocumentResult,
  ProcessingMode,
} from './src/types/ocr.ts';

const PORT = Number(process.env.PORT) || 3000;
const HOST = process.env.HOST || '127.0.0.1';
const MAX_FILE_SIZE_BYTES = 20 * 1024 * 1024;
const MODEL_ID = 'gemini-3.8-flash';

const SUPPORTED_MIME_TYPES = new Set([
  'application/pdf',
  'image/png',
  'image/jpeg',
  'image/webp',
]);

const EXTENSION_MIME_MAP: Record<string, string> = {
  '.pdf': 'application/pdf',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
};

interface OcrJobRecord {
  jobId: string;
  status: 'processing' | 'completed' | 'failed';
  createdAt: number;
  result?: OcrDocumentResult;
  error?: string;
  statusCode?: number;
  hint?: string;
  clientReference?: string;
}

const ledgerHistory: LedgerEntry[] = [];
const MAX_HISTORY_ENTRIES = 50;
const ocrJobs = new Map<string, OcrJobRecord>();
const MAX_JOB_ENTRIES = 100;

function normalizeMimeType(rawMime: string | undefined, fileName: string | undefined): string {
  const cleaned = (rawMime || '').split(';')[0].trim().toLowerCase();
  if (cleaned === 'image/jpg') {
    return 'image/jpeg';
  }
  if (SUPPORTED_MIME_TYPES.has(cleaned)) {
    return cleaned;
  }
  if (fileName) {
    const ext = path.extname(fileName).toLowerCase();
    if (EXTENSION_MIME_MAP[ext]) {
      return EXTENSION_MIME_MAP[ext];
    }
  }
  return cleaned;
}

function parseStringList(input: unknown): string[] {
  if (!input) return [];
  if (Array.isArray(input)) {
    return input
      .map((item) => String(item).trim())
      .filter((item) => item.length > 0);
  }
  if (typeof input === 'string') {
    const trimmed = input.trim();
    if (!trimmed) return [];
    if (trimmed.startsWith('[')) {
      try {
        const parsed = JSON.parse(trimmed);
        if (Array.isArray(parsed)) {
          return parsed.map((item) => String(item).trim()).filter(Boolean);
        }
      } catch {
        // Fall back to newline/comma splitting
      }
    }
    return trimmed
      .split(/\r?\n/)
      .map((line) => line.trim())
      .filter(Boolean);
  }
  return [];
}

function getAiClient(): GoogleGenAI {
  const apiKey = process.env.GEMINI_API_KEY;
  if (!apiKey) {
    const err = new Error('GEMINI_API_KEY is not configured on the server. Set it in Settings > Secrets.');
    (err as Error & { statusCode?: number }).statusCode = 503;
    throw err;
  }
  return new GoogleGenAI({
    apiKey,
    httpOptions: {
      headers: {
        'User-Agent': 'aistudio-build',
      },
    },
  });
}

const OCR_RESPONSE_SCHEMA = {
  type: Type.OBJECT,
  properties: {
    documentClassification: {
      type: Type.OBJECT,
      properties: {
        documentType: {
          type: Type.STRING,
          description: 'Category such as handwritten_note, commercial_invoice, inspection_form, identity_document, receipt, contract, medical_record, or general_document.',
        },
        summary: {
          type: Type.STRING,
          description: 'Concise 1-2 sentence factual summary of the document contents.',
        },
        languages: {
          type: Type.ARRAY,
          items: { type: Type.STRING },
          description: 'Detected languages in ISO or full English name.',
        },
        containsHandwriting: {
          type: Type.BOOLEAN,
          description: 'True if any handwritten text, notes, or manual checkbox marks appear.',
        },
        containsSignatures: {
          type: Type.BOOLEAN,
          description: 'True if one or more handwritten or digital signatures are visible.',
        },
        containsStamps: {
          type: Type.BOOLEAN,
          description: 'True if an official ink stamp, seal, or watermark is present.',
        },
        estimatedPages: {
          type: Type.INTEGER,
          description: 'Number of document pages inspected.',
        },
        legibilityScore: {
          type: Type.NUMBER,
          description: 'Overall scan/handwriting legibility score from 0 to 100.',
        },
      },
      required: [
        'documentType',
        'summary',
        'languages',
        'containsHandwriting',
        'containsSignatures',
        'containsStamps',
        'estimatedPages',
        'legibilityScore',
      ],
    },
    extraction: {
      type: Type.OBJECT,
      properties: {
        rawText: {
          type: Type.STRING,
          description: 'Full verbatim OCR transcription preserving reading order and line breaks.',
        },
        handwritingTranscript: {
          type: Type.STRING,
          description: 'Consolidated transcription of all handwritten segments, annotations, and signatures. Empty string if none.',
        },
        blocks: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              id: { type: Type.STRING },
              blockType: {
                type: Type.STRING,
                description: 'One of: header, paragraph, handwritten, table, signature, stamp, checkbox.',
              },
              text: { type: Type.STRING },
              confidence: { type: Type.NUMBER },
            },
            required: ['id', 'blockType', 'text', 'confidence'],
          },
        },
        fields: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              key: { type: Type.STRING },
              value: { type: Type.STRING },
              sourceType: {
                type: Type.STRING,
                description: 'One of: printed, handwritten, stamp, signature, inferred.',
              },
              confidence: { type: Type.NUMBER },
            },
            required: ['key', 'value', 'sourceType', 'confidence'],
          },
        },
        tables: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              title: { type: Type.STRING },
              headers: {
                type: Type.ARRAY,
                items: { type: Type.STRING },
              },
              rows: {
                type: Type.ARRAY,
                items: {
                  type: Type.ARRAY,
                  items: { type: Type.STRING },
                },
              },
            },
            required: ['title', 'headers', 'rows'],
          },
        },
        signatures: {
          type: Type.ARRAY,
          description: 'All handwritten ink signatures, digital signatures, or initial blocks detected in the document.',
          items: {
            type: Type.OBJECT,
            properties: {
              id: { type: Type.STRING },
              signerName: {
                type: Type.STRING,
                description: 'Transcribed name of the signer, or "[Unreadable Signature]" if illegible.',
              },
              roleOrLabel: {
                type: Type.STRING,
                description: 'Adjacent label or role such as Physician, Approved By, Inspector Signature, Verified By, or Signer.',
              },
              signedDate: {
                type: Type.STRING,
                description: 'Date written alongside the signature, or empty string if none.',
              },
              signatureType: {
                type: Type.STRING,
                description: 'One of: handwritten_ink, digital_signature, initials, electronic_typed.',
              },
              pageNumber: {
                type: Type.INTEGER,
                description: '1-indexed page number where the signature appears.',
              },
              locationHint: {
                type: Type.STRING,
                description: 'Brief spatial description such as Bottom right below APPROVED stamp.',
              },
              matchesPrintedName: {
                type: Type.STRING,
                description: 'One of: matched, mismatch, no_printed_reference.',
              },
              confidence: { type: Type.NUMBER },
              boundingBox: {
                type: Type.OBJECT,
                description: 'Normalized 0-1000 coordinates tightly enclosing the signature strokes.',
                properties: {
                  ymin: { type: Type.NUMBER },
                  xmin: { type: Type.NUMBER },
                  ymax: { type: Type.NUMBER },
                  xmax: { type: Type.NUMBER },
                },
                required: ['ymin', 'xmin', 'ymax', 'xmax'],
              },
            },
            required: [
              'id',
              'signerName',
              'roleOrLabel',
              'signedDate',
              'signatureType',
              'pageNumber',
              'locationHint',
              'matchesPrintedName',
              'confidence',
              'boundingBox',
            ],
          },
        },
      },
      required: ['rawText', 'handwritingTranscript', 'blocks', 'fields', 'tables', 'signatures'],
    },
    verification: {
      type: Type.OBJECT,
      properties: {
        overallStatus: {
          type: Type.STRING,
          description: 'One of: verified, needs_review, flagged.',
        },
        confidenceScore: {
          type: Type.NUMBER,
          description: 'Confidence score from 0 to 100 in the verification outcome.',
        },
        verdictSummary: {
          type: Type.STRING,
          description: 'Clear 1-3 sentence audit verdict explaining what passed, failed, or needs human review.',
        },
        anomalyFlags: {
          type: Type.ARRAY,
          items: { type: Type.STRING },
          description: 'List of detected discrepancies, illegible spans, missing required elements, or tampering indicators. Empty if none.',
        },
        checks: {
          type: Type.ARRAY,
          items: {
            type: Type.OBJECT,
            properties: {
              checkName: { type: Type.STRING },
              category: {
                type: Type.STRING,
                description: 'One of: mathematical, completeness, consistency, authenticity, legibility, custom_rule.',
              },
              status: {
                type: Type.STRING,
                description: 'One of: passed, failed, inconclusive.',
              },
              detail: { type: Type.STRING },
              evidence: { type: Type.STRING },
            },
            required: ['checkName', 'category', 'status', 'detail', 'evidence'],
          },
        },
      },
      required: ['overallStatus', 'confidenceScore', 'verdictSummary', 'anomalyFlags', 'checks'],
    },
  },
  required: ['documentClassification', 'extraction', 'verification'],
};

function buildPrompt(
  mode: ProcessingMode,
  verificationRules: string[],
  customFields: string[],
  languageHint: string,
  includeSignatures: boolean,
): string {
  const lines: string[] = [
    'You are VeriDoc OCR, a precision multimodal document OCR and AI verification engine.',
    'Carefully inspect the attached PDF, PNG, JPEG, or WebP document (including any cursive/print handwriting, tables, checkboxes, stamps, and signatures).',
    `Processing mode: ${mode}.`,
  ];

  if (languageHint) {
    lines.push(`Language hint from caller: ${languageHint}.`);
  }

  lines.push(
    'EXTRACTION INSTRUCTIONS:',
    '- Transcribe all visible printed and handwritten text verbatim in reading order into rawText.',
    '- Separate handwritten entries, clinical/inspector notes, and ink signatures into handwritingTranscript as well.',
    '- Populate blocks with major semantic sections (max 10 blocks total: header, paragraph, handwritten, table, signature, stamp, checkbox) and accurate confidence scores (0-100).',
    '- Extract structured key-value pairs into fields, specifying whether each field came from printed text, handwritten ink, a stamp, or a signature.',
    '- Extract any tabular structures (line items, checklists, schedules) into tables with accurate headers and rows.',
  );

  if (includeSignatures) {
    lines.push(
      '- SIGNATURE EXTRACTION INSTRUCTIONS (REQUIRED): Detect every handwritten ink signature, digital signature, or initial block in the document. Populate extraction.signatures with signerName, roleOrLabel, signedDate, signatureType, pageNumber (1-indexed), locationHint, matchesPrintedName (compare against printed signer/inspector/doctor names if present), confidence (0-100), and normalized boundingBox coordinates (ymin, xmin, ymax, xmax on a 0 to 1000 scale tightly enclosing the signature strokes). Also include signature fields (such as Signer Name, Signature Date, Role) in extraction.fields when present. Return an empty array if no signatures appear.',
    );
  } else {
    lines.push(
      '- SIGNATURE EXTRACTION: Caller requested includeSignatures=false; return an empty array for extraction.signatures.',
    );
  }

  if (customFields.length > 0) {
    lines.push(
      `PRIORITY TARGET FIELDS TO EXTRACT (include these in extraction.fields; if absent in the document, record value as "[NOT FOUND]" with low confidence): ${customFields.join(', ')}.`,
    );
  }

  lines.push(
    'VERIFICATION & AUDIT INSTRUCTIONS:',
    '- Perform thorough integrity checks appropriate to the document type: mathematical arithmetic verification (line items -> subtotal -> tax -> total), date/chronology consistency, completeness of required fields/signatures/checkboxes, legibility assessment, and visual anomaly/alteration detection.',
    '- Only set overallStatus to "verified" when all critical checks pass and there are no unresolved anomalies. Use "needs_review" for minor legibility ambiguity or inconclusive checks. Use "flagged" if arithmetic fails, a required rule fails, or tampering/contradictions are detected.',
  );

  if (verificationRules.length > 0) {
    lines.push('CALLER VERIFICATION RULES (MUST evaluate EVERY rule below as a separate check with category "custom_rule" and concrete evidence quoted from the document):');
    verificationRules.forEach((rule, index) => {
      lines.push(`${index + 1}. ${rule}`);
    });
  } else {
    lines.push('Include at least 3 concrete verification checks covering completeness, consistency/arithmetic (if numbers exist), legibility, and signature/authenticity signals.');
  }

  if (mode === 'ocr_only') {
    lines.push('Because mode is ocr_only, prioritize exhaustive verbatim OCR and structured extraction, and provide baseline legibility/completeness verification checks.');
  } else if (mode === 'verify_only') {
    lines.push('Because mode is verify_only, focus on thorough verification checks, anomaly detection, and evidence fields; keep blocks to at most 3 summary regions.');
  }

  lines.push('Never invent or hallucinate text that is not visible in the document. If a handwritten word is genuinely illegible, mark it as [illegible] and lower confidence accordingly.');

  return lines.join('\n');
}

async function runGeminiOcrWithRetry(
  buffer: Buffer,
  mimeType: string,
  fileName: string,
  mode: ProcessingMode,
  verificationRules: string[],
  customFields: string[],
  languageHint: string,
  includeSignatures: boolean,
): Promise<OcrDocumentResult> {
  const ai = getAiClient();
  const startedAt = Date.now();
  const base64Data = buffer.toString('base64');
  const prompt = buildPrompt(mode, verificationRules, customFields, languageHint, includeSignatures);

  let lastError: unknown;
  // This runs inside an asynchronous job, so allow temporary model-capacity
  // errors a wider recovery window without making the upload request wait.
  const maxAttempts = 5;

  for (let attempt = 1; attempt <= maxAttempts; attempt++) {
    try {
      const response = await ai.models.generateContent({
        model: MODEL_ID,
        contents: {
          parts: [
            {
              inlineData: {
                mimeType,
                data: base64Data,
              },
            },
            {
              text: prompt,
            },
          ],
        },
        config: {
          systemInstruction:
            'You are a deterministic document OCR and verification engine. Always return valid JSON matching the requested schema. Quote exact observed values as evidence.',
          responseMimeType: 'application/json',
          responseSchema: OCR_RESPONSE_SCHEMA,
          thinkingConfig: {
            thinkingLevel: ThinkingLevel.LOW,
          },
        },
      });

      const rawJson = (response.text ?? '').trim();
      if (!rawJson) {
        throw new Error('Empty response returned by Gemini vision model.');
      }

      const parsed = JSON.parse(rawJson) as {
        documentClassification: OcrDocumentResult['documentClassification'];
        extraction: OcrDocumentResult['extraction'];
        verification: OcrDocumentResult['verification'];
      };

      const durationMs = Date.now() - startedAt;
      const requestId = `req_${crypto.randomBytes(6).toString('hex')}`;
      const processedAt = new Date().toISOString();

      const signaturesList = includeSignatures ? (parsed.extraction.signatures ?? []) : [];
      const result: OcrDocumentResult = {
        requestId,
        processedAt,
        durationMs,
        fileName,
        mimeType,
        fileSizeBytes: buffer.length,
        mode,
        documentClassification: parsed.documentClassification,
        extraction: {
          ...parsed.extraction,
          signatures: signaturesList,
        },
        verification: parsed.verification,
      };

      const ledgerEntry: LedgerEntry = {
        requestId,
        fileName,
        mimeType,
        fileSizeBytes: buffer.length,
        documentType: result.documentClassification.documentType,
        containsHandwriting: result.documentClassification.containsHandwriting,
        containsSignatures: result.documentClassification.containsSignatures || signaturesList.length > 0,
        signatureCount: signaturesList.length,
        overallStatus: result.verification.overallStatus,
        confidenceScore: result.verification.confidenceScore,
        durationMs,
        processedAt,
        result,
      };

      ledgerHistory.unshift(ledgerEntry);
      if (ledgerHistory.length > MAX_HISTORY_ENTRIES) {
        ledgerHistory.length = MAX_HISTORY_ENTRIES;
      }

      return result;
    } catch (error) {
      lastError = error;
      const status = (error as { status?: number })?.status;
      // Retry only on transient rate limit or 5xx errors
      if (attempt < maxAttempts && (status === 429 || (status && status >= 500))) {
        const retryDelayMs = Math.min(2000 * (2 ** (attempt - 1)), 15000);
        await new Promise((resolve) => setTimeout(resolve, retryDelayMs));
        continue;
      }
      break;
    }
  }

  throw lastError;
}

const upload = multer({
  storage: multer.memoryStorage(),
  limits: {
    fileSize: MAX_FILE_SIZE_BYTES,
  },
});

function requireOcrApiToken(req: Request, res: Response, next: NextFunction): void {
  const expectedToken = process.env.OCR_API_TOKEN;
  const authorization = req.header('authorization') || '';
  const suppliedToken = authorization.match(/^Bearer\s+(.+)$/i)?.[1] || '';

  if (!expectedToken) {
    res.status(503).json({ error: 'OCR_API_TOKEN is not configured on the OCR service.' });
    return;
  }

  const expected = Buffer.from(expectedToken);
  const supplied = Buffer.from(suppliedToken);
  if (expected.length !== supplied.length || !crypto.timingSafeEqual(expected, supplied)) {
    res.status(401).json({ error: 'Unauthorized OCR API request.' });
    return;
  }

  next();
}

function parseOcrRequest(
  req: Request,
  forcedMode?: ProcessingMode,
): {
  buffer: Buffer;
  fileName: string;
  mimeType: string;
  mode: ProcessingMode;
  verificationRules: string[];
  customFields: string[];
  languageHint: string;
  includeSignatures: boolean;
  clientReference?: string;
} | { errorStatus: number; errorBody: Record<string, unknown> } {
  let buffer: Buffer | null = null;
  let fileName = 'document';
  let mimeType = '';
  let mode: ProcessingMode = forcedMode || 'extract_and_verify';
  let verificationRules: string[] = [];
  let customFields: string[] = [];
  let languageHint = '';
  let includeSignatures = true;
  let clientReference: string | undefined;

  if (req.file) {
    buffer = req.file.buffer;
    fileName = req.file.originalname || 'uploaded-document';
    mimeType = normalizeMimeType(req.file.mimetype, fileName);
    const bodyMode = String(req.body?.mode || '').trim();
    if (!forcedMode && (bodyMode === 'extract_and_verify' || bodyMode === 'ocr_only' || bodyMode === 'verify_only')) {
      mode = bodyMode;
    }
    verificationRules = parseStringList(req.body?.verificationRules ?? req.body?.rules);
    customFields = parseStringList(req.body?.customFields ?? req.body?.fields);
    languageHint = String(req.body?.language || '').trim();
    if (req.body?.includeSignatures !== undefined) {
      includeSignatures = String(req.body.includeSignatures).trim().toLowerCase() !== 'false';
    }
    clientReference = req.body?.clientReference ? String(req.body.clientReference).trim() : undefined;
  } else if (req.body && typeof req.body.fileBase64 === 'string') {
    const rawBase64 = req.body.fileBase64.replace(/^data:[^;]+;base64,/, '');
    buffer = Buffer.from(rawBase64, 'base64');
    fileName = String(req.body.fileName || 'api-document').trim();
    mimeType = normalizeMimeType(req.body.mimeType, fileName);
    const bodyMode = String(req.body.mode || '').trim();
    if (!forcedMode && (bodyMode === 'extract_and_verify' || bodyMode === 'ocr_only' || bodyMode === 'verify_only')) {
      mode = bodyMode;
    }
    verificationRules = parseStringList(req.body.verificationRules ?? req.body.rules);
    customFields = parseStringList(req.body.customFields ?? req.body.fields);
    languageHint = String(req.body.language || '').trim();
    if (req.body.includeSignatures !== undefined) {
      includeSignatures = req.body.includeSignatures !== false && String(req.body.includeSignatures).trim().toLowerCase() !== 'false';
    }
    clientReference = req.body.clientReference ? String(req.body.clientReference).trim() : undefined;
  }

  if (!buffer || buffer.length === 0) {
    return {
      errorStatus: 400,
      errorBody: {
        error: 'No document payload provided.',
        hint: 'Send multipart/form-data with field "file" (PDF, PNG, JPEG, WebP) or application/json with "fileBase64" and "mimeType".',
      },
    };
  }

  if (!SUPPORTED_MIME_TYPES.has(mimeType)) {
    return {
      errorStatus: 415,
      errorBody: {
        error: `Unsupported document format: ${mimeType || 'unknown'}.`,
        supportedFormats: Array.from(SUPPORTED_MIME_TYPES),
      },
    };
  }

  return {
    buffer,
    fileName,
    mimeType,
    mode,
    verificationRules,
    customFields,
    languageHint,
    includeSignatures,
    clientReference,
  };
}

function formatErrorPayload(error: unknown): {
  statusCode: number;
  message: string;
  hint?: string;
} {
  const errObj = error as { status?: number; statusCode?: number; message?: string };
  const rawCode = errObj.statusCode || errObj.status || 500;
  const statusCode = rawCode >= 400 && rawCode < 600 ? rawCode : 500;
  let message = errObj.message || 'Document OCR and verification failed.';
  if (message.startsWith('{')) {
    try {
      const parsed = JSON.parse(message) as { error?: { message?: string } };
      if (parsed?.error?.message) {
        message = parsed.error.message;
      }
    } catch {
      // Keep original message
    }
  }
  let hint: string | undefined;
  if (statusCode === 400 || statusCode === 403) {
    hint = 'Verify your GEMINI_API_KEY in Settings > Secrets.';
  } else if (statusCode === 429) {
    hint = 'Quota exceeded. Wait briefly or review your API key quota in Settings > Secrets.';
  } else if (statusCode === 503) {
    hint = 'Upstream model capacity is temporarily busy. Please click Retry Request in a few seconds.';
  }
  return { statusCode, message, hint };
}

function createAsyncJob(
  parsed: {
    buffer: Buffer;
    fileName: string;
    mimeType: string;
    mode: ProcessingMode;
    verificationRules: string[];
    customFields: string[];
    languageHint: string;
    includeSignatures: boolean;
    clientReference?: string;
  },
): OcrJobRecord {
  const jobId = `job_${crypto.randomBytes(6).toString('hex')}`;
  const job: OcrJobRecord = {
    jobId,
    status: 'processing',
    createdAt: Date.now(),
    clientReference: parsed.clientReference,
  };
  ocrJobs.set(jobId, job);
  if (ocrJobs.size > MAX_JOB_ENTRIES) {
    const oldestKey = ocrJobs.keys().next().value;
    if (oldestKey) ocrJobs.delete(oldestKey);
  }

  void runGeminiOcrWithRetry(
    parsed.buffer,
    parsed.mimeType,
    parsed.fileName,
    parsed.mode,
    parsed.verificationRules,
    parsed.customFields,
    parsed.languageHint,
    parsed.includeSignatures,
  )
    .then((result) => {
      job.status = 'completed';
      job.result = result;
      void sendCompletionCallback(job, { status: 'completed', result });
    })
    .catch((error) => {
      const formatted = formatErrorPayload(error);
      job.status = 'failed';
      job.error = formatted.message;
      job.statusCode = formatted.statusCode;
      job.hint = formatted.hint;
      void sendCompletionCallback(job, { status: 'failed', error: formatted.message });
    });

  return job;
}

async function sendCompletionCallback(
  job: OcrJobRecord,
  completion: { status: 'completed'; result: OcrDocumentResult } | { status: 'failed'; error: string },
): Promise<void> {
  const callbackUrl = process.env.LARAVEL_CALLBACK_URL;
  const token = process.env.OCR_API_TOKEN;
  if (!callbackUrl || !token || !job.clientReference) {
    return;
  }

  try {
    const target = new URL(callbackUrl);
    if (!['http:', 'https:'].includes(target.protocol)
      || !['localhost', '127.0.0.1', '[::1]'].includes(target.hostname)) {
      console.error('LARAVEL_CALLBACK_URL must point to a trusted local Laravel endpoint.');
      return;
    }

    const response = await fetch(target, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        clientReference: job.clientReference,
        jobId: job.jobId,
        ...completion,
      }),
      signal: AbortSignal.timeout(10000),
    });

    if (!response.ok) {
      console.error(`Laravel callback for OCR job ${job.jobId} returned HTTP ${response.status}.`);
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Unknown callback error.';
    console.error(`Laravel callback for OCR job ${job.jobId} failed: ${message}`);
  }
}

async function handleOcrEndpoint(
  req: Request,
  res: Response,
  forcedMode?: ProcessingMode,
  forceAsync = false,
): Promise<void> {
  const parsed = parseOcrRequest(req, forcedMode);
  if ('errorStatus' in parsed) {
    res.status(parsed.errorStatus).json(parsed.errorBody);
    return;
  }

  const wantsAsync =
    forceAsync ||
    req.query.async === 'true' ||
    req.body?.async === true ||
    req.body?.async === 'true';

  if (wantsAsync) {
    const job = createAsyncJob(parsed);
    res.status(202).json({
      jobId: job.jobId,
      status: job.status,
      pollUrl: `/api/v1/ocr/jobs/${job.jobId}`,
    });
    return;
  }

  try {
    const result = await runGeminiOcrWithRetry(
      parsed.buffer,
      parsed.mimeType,
      parsed.fileName,
      parsed.mode,
      parsed.verificationRules,
      parsed.customFields,
      parsed.languageHint,
      parsed.includeSignatures,
    );

    res.status(200).json(result);
  } catch (error) {
    const formatted = formatErrorPayload(error);
    res.status(formatted.statusCode).json({
      error: formatted.message,
      statusCode: formatted.statusCode,
      ...(formatted.hint ? { hint: formatted.hint } : {}),
    });
  }
}

async function startServer(): Promise<void> {
  const app = express();
  app.use(express.json({ limit: '25mb' }));
  app.use(express.urlencoded({ extended: true, limit: '25mb' }));

  // API v1 Routes
  app.get('/api/v1/health', (_req: Request, res: Response) => {
    res.json({
      status: 'ok',
      service: 'VeriDoc OCR API',
      model: MODEL_ID,
      supportedFormats: Array.from(SUPPORTED_MIME_TYPES),
      maxFileSizeBytes: MAX_FILE_SIZE_BYTES,
      endpoints: [
        { method: 'POST', path: '/api/v1/ocr/process', description: 'Unified OCR extraction + AI verification' },
        { method: 'POST', path: '/api/v1/ocr/extract', description: 'Structured OCR & handwriting extraction' },
        { method: 'POST', path: '/api/v1/ocr/verify', description: 'AI document authenticity & rule verification' },
        { method: 'POST', path: '/api/v1/ocr/jobs', description: 'Async document job submission (returns jobId for polling)' },
        { method: 'GET', path: '/api/v1/ocr/jobs/:jobId', description: 'Poll async OCR/verification job status and result' },
        { method: 'GET', path: '/api/v1/ocr/history', description: 'Session audit ledger of processed documents' },
        { method: 'GET', path: '/api/v1/health', description: 'Service capabilities and health status' },
      ],
    });
  });

  // Keep document processing and the in-memory audit ledger private to Laravel.
  app.use('/api/v1/ocr', requireOcrApiToken);

  app.post('/api/v1/ocr/process', upload.single('file'), (req: Request, res: Response) => {
    void handleOcrEndpoint(req, res);
  });

  app.post('/api/v1/ocr/extract', upload.single('file'), (req: Request, res: Response) => {
    void handleOcrEndpoint(req, res, 'ocr_only');
  });

  app.post('/api/v1/ocr/verify', upload.single('file'), (req: Request, res: Response) => {
    void handleOcrEndpoint(req, res, 'verify_only');
  });

  app.post('/api/v1/ocr/jobs', upload.single('file'), (req: Request, res: Response) => {
    void handleOcrEndpoint(req, res, undefined, true);
  });

  app.get('/api/v1/ocr/jobs/:jobId', (req: Request, res: Response) => {
    const job = ocrJobs.get(req.params.jobId);
    if (!job) {
      res.status(404).json({ error: 'Job not found or expired.' });
      return;
    }
    res.json({
      jobId: job.jobId,
      status: job.status,
      elapsedMs: Date.now() - job.createdAt,
      ...(job.result ? { result: job.result } : {}),
      ...(job.error ? { error: job.error, statusCode: job.statusCode, hint: job.hint } : {}),
    });
  });

  app.get('/api/v1/ocr/history', (_req: Request, res: Response) => {
    res.json({ entries: ledgerHistory });
  });

  app.delete('/api/v1/ocr/history', (_req: Request, res: Response) => {
    ledgerHistory.length = 0;
    res.json({ cleared: true });
  });

  // Multer and JSON body error middleware
  app.use((err: Error & { code?: string; status?: number }, _req: Request, res: Response, next: NextFunction) => {
    if (err instanceof multer.MulterError) {
      if (err.code === 'LIMIT_FILE_SIZE') {
        res.status(413).json({
          error: 'Uploaded file exceeds the 20 MB maximum limit.',
        });
        return;
      }
      res.status(400).json({ error: err.message });
      return;
    }
    if (err) {
      res.status(err.status || 400).json({ error: err.message || 'Malformed request.' });
      return;
    }
    next();
  });

  app.use((_req: Request, res: Response) => {
    res.status(404).json({ error: 'Not found.' });
  });

  app.listen(PORT, HOST, () => {
    console.log(`VeriDoc OCR API listening on http://${HOST}:${PORT}`);
  });
}

void startServer();
