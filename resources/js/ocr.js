// resources/js/ocr.js
import * as ocr from '@paddlejs-models/ocr';

let isInitialized = false;

export async function initOCR() {
    if (!isInitialized) {
        console.log('[OCR] Initializing OCR model...');
        await ocr.init();
        isInitialized = true;
    } else {
        console.log('[OCR] Already initialized, skipping...');
    }
    return ocr;
}
