let video2img = function(video_dom){
    if(typeof(video_dom) == "undefined" || video_dom.length < 1){
        return '';
    }
    const canvas = document.createElement("canvas");
    const canvasCtx = canvas.getContext("2d")

    const ratio = window.devicePixelRatio || 1;
    canvasCtx.scale(ratio, ratio);

// canvas大小与图片大小保持一致，截图没有多余
    canvas.width = video_dom.offsetWidth * ratio;
    canvas.height = video_dom.offsetHeight * ratio;

    canvasCtx.drawImage(video_dom, 0, 0, canvas.width, canvas.height)
    return canvas.toDataURL("image/png");
}