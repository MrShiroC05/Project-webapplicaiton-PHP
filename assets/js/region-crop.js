// File role: crop validation and square image handling for region forms.

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('regionForm') || document.getElementById('regionUpdateForm');
    if (!form) {
        return;
    }

    const fileInput = form.querySelector('input[type="file"]');
    const cropPreviewWrapper = document.getElementById('cropPreviewWrapper');
    const cropImage = document.getElementById('cropImage');
    const hiddenImageData = document.getElementById('regionImageData');
    const applyCropBtn = document.getElementById('applyCropBtn');

    if (!fileInput || !cropPreviewWrapper || !cropImage || !hiddenImageData || !applyCropBtn) {
        return;
    }

    let cropper = null;

    form.addEventListener('submit', function (event) {
        if (fileInput.files.length > 0 && !hiddenImageData.value) {
            event.preventDefault();
            alert('รูปภาพต้องเป็นสัดส่วน 1:1 ก่อนบันทึก หากรูปไม่ใช่สี่เหลี่ยมจัตุรัส กรุณาเลือกส่วนที่ต้องการให้เห็น');
            return;
        }
    });

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            cropImage.src = event.target.result;
            cropPreviewWrapper.style.display = 'block';

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                background: false,
                guides: true,
                center: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false
            });

            const img = new Image();
            img.onload = function () {
                const isSquare = img.naturalWidth === img.naturalHeight;
                if (isSquare) {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    hiddenImageData.value = canvas.toDataURL('image/png');
                    cropPreviewWrapper.style.display = 'none';
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                } else {
                    hiddenImageData.value = '';
                }
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    applyCropBtn.addEventListener('click', function () {
        if (!cropper) {
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 800,
            imageSmoothingQuality: 'high'
        });

        hiddenImageData.value = canvas.toDataURL('image/png');
        cropPreviewWrapper.style.display = 'none';
        cropper.destroy();
        cropper = null;
    });
});
