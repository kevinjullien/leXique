"use strict"

function onTray(e) {
    let data = getLibelleOnDataSet(e.target);
    console.log(data)
    document.getElementById(data).click();
}

Array.from(document.getElementsByClassName("trays")).forEach(e => e.addEventListener("click", onTray));

function getLibelleOnDataSet(elem){
    if (elem.dataset.data)
        return elem.dataset.data;
    return getLibelleOnDataSet(elem.parentNode);
}