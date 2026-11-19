# AutoDoc Mobile

Flutter student app connected to the Laravel API in `../autodoc_web`.

## Laravel setup

The Laravel backend provides Microsoft mobile sign-in, student profile and dashboard data, document upload/listing, temporary document links, and notifications. It stores uploaded files in the same Supabase S3 bucket as the web app.

The mobile API uses bearer tokens in the `mobile_api_tokens` table. Its migration is:

```powershell
cd C:\xampp\htdocs\AutoDoc\autodoc_web
php artisan migrate
```

Set these values in Laravel's local `.env` and Microsoft Entra app registration:

- `MICROSOFT_CLIENT_ID` and `MICROSOFT_TENANT_ID` must match the web app registration.
- `MICROSOFT_MOBILE_ANDROID_REDIRECT_URI` must exactly match the Android redirect URI registered as a mobile/native platform in Entra. It uses the Android application ID and signing certificate hash.
- `MICROSOFT_MOBILE_IOS_REDIRECT_URI` must match the iOS URL scheme and redirect URI registered in Entra.

Do not put a Microsoft client secret, Supabase service role key, or storage secret in Flutter.

## Run against Laravel on a phone

The phone must be on the same Wi-Fi network as the PC. Find the PC's IPv4 address with `ipconfig`, then start Laravel so the phone can reach it:

```powershell
cd C:\xampp\htdocs\AutoDoc\autodoc_web
php artisan serve --host=0.0.0.0 --port=8000
```

Run Flutter with the PC's LAN IPv4 address (replace the example address):

```powershell
cd C:\xampp\htdocs\AutoDoc\autodoc_mobile
flutter pub get
flutter run --dart-define=AUTODOC_API_BASE_URL=http://192.168.1.20:8000
```

The default API URL is `http://127.0.0.1:8000`, which only reaches the same device. Android debug builds allow local HTTP; production should use HTTPS. Windows Firewall must allow inbound connections to port 8000 on the private network.

## App behavior

- Microsoft signs the student into Laravel, which creates the AutoDoc student record if needed and returns a revocable mobile API token.
- The token is stored with platform secure storage and sent in the Authorization header.
- Upload rules and document types come from Laravel. Documents are uploaded to Laravel, then stored in Supabase Storage by the server.
- The mobile API only returns a student's own records and file links.
