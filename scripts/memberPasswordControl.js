"use strict"

let addBtn = document.getElementById("addMemberBtn")
let usrField = document.getElementById("utilisateur")
let pswField = document.getElementById("password")
let pswControl = document.getElementById("passwordControl")

usrField.addEventListener('keyup', onEditInput)
pswField.addEventListener('keyup', onEditInput)
pswControl.addEventListener('keyup', onEditInput)

function onEditInput() {
    addBtn.disabled = !(usrField.value !== "" && pswField.value !== "" && pswField.value === pswControl.value);
}