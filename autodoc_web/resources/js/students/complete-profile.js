document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('profile_picture');
    const preview = document.getElementById('profilePicturePreview');
    let currentPreviewUrl = null;

    input?.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !preview || !file.type.startsWith('image/')) return;

        if (currentPreviewUrl) URL.revokeObjectURL(currentPreviewUrl);
        currentPreviewUrl = URL.createObjectURL(file);
        preview.src = currentPreviewUrl;
    });
});
