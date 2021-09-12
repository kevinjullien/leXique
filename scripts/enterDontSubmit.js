"use strict"

Array.from(document.getElementsByClassName('enterDontSubmit')).forEach(elem => {
        elem.addEventListener('keydown', (k) => {
            if (k.keyCode === 13) k.preventDefault();
        })
    }
)
