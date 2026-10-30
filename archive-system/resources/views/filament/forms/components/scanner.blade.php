<div x-data="{
    isScanning: false,
    previewUrl: null,
    scan() {
        this.isScanning = true;
        scanner.scan((successful, mesg, response) => {
            if (!successful) {
                alert('خطأ في الاتصال بالسكانر: ' + mesg);
                this.isScanning = false;
                return;
            }

            let scannedImage = response.getScannedImages()[0];
            this.previewUrl = scannedImage.src;
            this.uploadToFilament(scannedImage.src);
            this.isScanning = false;
        }, {
            'output_settings': [{ 'type': 'return-base64', 'format': 'jpg' }]
        });
    },
    async uploadToFilament(base64Data) {
        const response = await fetch(base64Data);
        const blob = await response.blob();
        const file = new File([blob], 'scanned_' + Date.now() + '.jpg', { type: 'image/jpeg' });

        // ربط الملف بمكون الرفع الخاص بفلامنت
        const input = document.querySelector('input[type=file]');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}">
    <div class="p-4 border border-gray-200 rounded-xl  shadow-sm flex flex-col items-center">
        <button type="button" @click="scan()"
            class="px-6 py-3 bg-primary-600 text-white rounded-lg flex items-center gap-2 hover:bg-primary-700 transition"
            :disabled="isScanning">
            <span x-show="!isScanning" class="flex items-center gap-2">
                <x-heroicon-o-computer-desktop class="w-5 h-5 text-white" />
                بدء عملية المسح الضوئي (Scanner)
            </span>
            <span x-show="isScanning">جاري سحب المستند...</span>
        </button>

        <div x-show="previewUrl" class="mt-4 animate-pulse-slow">
            <p class="text-sm text-green-600 mb-2">تم التقاط المستند بنجاح:</p>
            <img :src="previewUrl" class="max-h-60 rounded border-2 border-primary-500 shadow-xl" />
        </div>
    </div>
</div>
