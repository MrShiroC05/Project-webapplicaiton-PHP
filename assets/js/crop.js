// File role: flexible crop validation for region, champion, and race forms.

document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('[data-crop-form]');
    // Function to get output size based on entity type
    const getOutputSize = function (entityType) {
        if (entityType === 'region') {
            return { width: 800, height: 800 };
        }

        return { width: 1200, height: 900 };
    };

    forms.forEach(function (form) {
        const entityType = (form.dataset.cropType || 'region').toLowerCase();
        const aspectRatio = entityType === 'region' ? 1 : 4 / 3;
        const outputSize = getOutputSize(entityType);
        const fileInput = form.querySelector('input[type="file"]');
        const cropPreviewWrapper = form.querySelector('[data-crop-preview]');
        const cropImage = form.querySelector('[data-crop-image]');
        const hiddenImageData = form.querySelector('[data-crop-data]');
        const applyCropBtn = form.querySelector('[data-crop-apply]');

        if (!fileInput || !cropPreviewWrapper || !cropImage || !hiddenImageData || !applyCropBtn) {
            return;
        }

        let cropper = null;

        form.addEventListener('submit', function (event) {
            if (fileInput.files.length > 0 && !hiddenImageData.value) {
                event.preventDefault();
                alert(entityType === 'region'
                    ? 'รูปภาพต้องถูก crop เป็นสัดส่วน 1:1 ก่อนบันทึก'
                    : 'รูปภาพต้องถูก crop เป็นสัดส่วน 4:3 ก่อนบันทึก');
                return;
            }
        });

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            hiddenImageData.value = '';

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                cropImage.src = event.target.result;
                cropPreviewWrapper.style.display = 'block';

                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                cropper = new Cropper(cropImage, {
                    aspectRatio: aspectRatio,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    background: false,
                    guides: true,
                    center: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    minCropBoxWidth: 120,
                    minCropBoxHeight: 120
                });
            };
            reader.readAsDataURL(file);
        });

        applyCropBtn.addEventListener('click', function () {
            if (!cropper) {
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                width: outputSize.width,
                height: outputSize.height,
                imageSmoothingQuality: 'high'
            });

            hiddenImageData.value = canvas.toDataURL('image/png');
            cropPreviewWrapper.style.display = 'none';
            cropper.destroy();
            cropper = null;
        });
    });
});
