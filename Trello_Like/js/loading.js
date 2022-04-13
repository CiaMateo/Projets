function fadeOutEffect(target, time) {
    var fadeEffect = setInterval(function () {
        if (!target.style.opacity) {
            target.style.opacity = 1;
        }
        if (target.style.opacity > 0) {
            target.style.opacity -= 5 / time;
        } else {
            target.remove();
            clearInterval(fadeEffect);
        }
    }, 5);
}

window.addEventListener("load", function () {
    setTimeout(() => {
        fadeOutEffect(document.getElementById("loadingScreen"), 500);
    });
});