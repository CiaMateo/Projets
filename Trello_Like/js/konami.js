function onKonamiCode(cb) {
    var input = '';
    var key = '38384040373937396665';
    document.addEventListener('keydown', function (e) {
        input += ("" + e.keyCode);
        if (input === key) {
            return cb();
        }
        if (!key.indexOf(input)) return;
        input = ("" + e.keyCode);
    });
}

onKonamiCode(activateCheats)

function activateCheats() {
    var audio = new Audio('assets/sounds/wii-sports.mp3');
    audio.volume = 0.1;
    audio.play();
}