import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/admin/dashboard.css',
                'resources/css/admin/review.css',
                'resources/css/admin/settings.css',
                'resources/css/admin/student-accounts.css',
                'resources/css/admin/student-docs.css',
                'resources/css/admin/student-documents.css',
                'resources/css/admin/submissions.css',
                'resources/css/admin/verification.css',
                'resources/css/components/admin-sidebar.css',
                'resources/css/components/student-sidebar.css',
                'resources/css/layouts/master.css',
                'resources/css/student/account.css',
                'resources/css/student/dashboard.css',
                'resources/css/student/documents.css',
                'resources/css/student/notifications.css',
                'resources/css/student/upload.css',
                'resources/css/welcome.css',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/admin-sidebar.js',
                'resources/js/admin/review.js',
                'resources/js/admin/settings.js',
                'resources/js/admin/student-accounts.js',
                'resources/js/admin/student-docs.js',
                'resources/js/admin/student-documents.js',
                'resources/js/admin/submissions.js',
                'resources/js/admin/verification.js',
                'resources/js/students/accounts.js',
                'resources/js/students/dashboard.js',
                'resources/js/students/documents.js',
                'resources/js/students/notifications.js',
                'resources/js/students/upload.js',
                'resources/js/students/student-sidebar.js',
                'resources/js/welcome.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
