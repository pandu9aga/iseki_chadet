import './bootstrap';
import { initOCR } from './ocr';

window.runOCR = async function () {
    const image = document.getElementById('uploadedImage');
    const resultEl = document.getElementById('result');
    resultEl.innerText = 'Loading model...';

    try {
        // Model initialization
        const ocr = await initOCR();

        // Get the text recognition result API, img is the user's upload picture, and option is an optional parameter
        // option.canvas as HTMLElementCanvas：if the user needs to draw the selected area of the text box, pass in the canvas element
        // option.style as object：if the user needs to configure the canvas style, pass in the style object
        // option.style.strokeStyle as string：select a color for the text box
        // option.style.lineWidth as number：width of selected line segment in text box
        // option.style.fillStyle as string：select the fill color for the text box
        // const res = await ocr.recognize(image);
        // character recognition results
        // console.log(res.text);
        // text area points
        // console.log(res.points);

        // await ocr.load();
        resultEl.innerText = 'Detecting text...';
        // const result = await ocr.detect(image);
        const res = await ocr.recognize(image);
        resultEl.innerText = res.text || 'Tidak ada teks terdeteksi';
    } catch (err) {
        resultEl.innerText = 'Gagal: ' + err.message;
    }
};
