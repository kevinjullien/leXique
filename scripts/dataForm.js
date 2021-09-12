"use strict"

let maxContent = '255';

let antonymesNbr = document.getElementsByClassName("form-control antonymes").length;
let synonymesNbr = document.getElementsByClassName("form-control synonymes").length;
let periodesNbr = document.getElementsByClassName("form-control periodes").length;
let champsLexicauxNbr = document.getElementsByClassName("form-control champsLexicaux").length;
let sieclesNbr = document.getElementsByClassName("form-control siecles").length;
let variantsNbr = document.getElementsByClassName("form-control variants").length;
let referencesNbr = 1;
let clicksOnDeleteReferenceBtn = 0;

let latestAntonyme = document.getElementById("antonyme" + antonymesNbr);
let latestSynonyme = document.getElementById("synonyme" + synonymesNbr);
let latestPeriode = document.getElementById("periode" + periodesNbr);
let latestChampLexical = document.getElementById("champLexical" + champsLexicauxNbr);
let latestSiecle = document.getElementById("siecle" + sieclesNbr);
let latestVariant = document.getElementById("variant" + variantsNbr);
let latestVariantType = document.getElementById("variantType" + variantsNbr);
let latestReferenceFile = document.getElementById("refFile" + referencesNbr);
let latestReferenceLien = document.getElementById("refLien" + referencesNbr);
let latestReferenceDateEd = document.getElementById("refDateEdition" + referencesNbr);
let latestReferenceLieuEd = document.getElementById("refLieuEdition" + referencesNbr);
let latestReferencePage = document.getElementById("refPage" + referencesNbr);
let latestReferenceEditeur = document.getElementById("refEditeur" + referencesNbr);
let latestReferenceAuteur = document.getElementById("refAuteur" + referencesNbr);
let latestReferenceTitre = document.getElementById("refTitre" + referencesNbr);

let divAntonymes = document.getElementById("divAntonymes");
let divSynonymes = document.getElementById("divSynonymes");
let divPeriodes = document.getElementById("divPeriodes");
let divChampsLexicaux = document.getElementById("divChampsLexicaux");
let divSiecles = document.getElementById("divSiecles");
let divVariants = document.getElementById("divVariants");
let divReferences = document.getElementById("divReferences");

let submitBtn = document.getElementById("validationBtn");

let words = [];
Array.from(document.getElementById("synonymeOptions").options).forEach(e => {
    words.push(e.value)
});
let champsLexicaux = [];
Array.from(document.getElementById("champLexicalOptions").options).forEach(e => {
    champsLexicaux.push(e.value)
});
let periodes = [];
Array.from(document.getElementById("periodeOptions").options).forEach(e => {
    periodes.push(e.value)
});
let siecles = [];
Array.from(document.getElementById("siecleOptions").options).forEach(e => {
    siecles.push(e.value)
});

function addEventListenerOnAllFieldsOfTheLatestReference() {
    latestReferenceFile.addEventListener("change", onWordInput);
    latestReferenceLien.addEventListener("keyup", onWordInput);
    latestReferenceDateEd.addEventListener("keyup", onWordInput);
    latestReferenceLieuEd.addEventListener("keyup", onWordInput);
    latestReferencePage.addEventListener("keyup", onWordInput);
    latestReferenceEditeur.addEventListener("keyup", onWordInput);
    latestReferenceAuteur.addEventListener("keyup", onWordInput);
    latestReferenceTitre.addEventListener("keyup", onWordInput);
}

function filterNewOptions(alreadyChosenOptions, targetName) {
    let options = [];
    switch (targetName) {
        case "periode":
            periodes.forEach(w => {
                if (!alreadyChosenOptions.includes(w))
                    options += `<option value="${w}">`
            })
            break;
        case "champLexical":
            champsLexicaux.forEach(w => {
                if (!alreadyChosenOptions.includes(w))
                    options += `<option value="${w}">`
            })
            break;
        case "siecle":
            siecles.forEach(w => {
                if (!alreadyChosenOptions.includes(w))
                    options += `<option value="${w}">`
            })
            break;
        case "synonyme":
        case "antonyme":
            words.forEach(w => {
                if (!alreadyChosenOptions.includes(w))
                    options += `<option value="${w}">`
            })
            break;
    }
    return options;
}

function filterAlreadyChosenOptions(targetName) {
    let options = [];
    let string = targetName + "s";
    if (targetName === "champLexical") string = "champsLexicaux";
    Array.from(document.getElementsByClassName(string))
        .forEach(
            input => {
                options.push(input.value)
            }
        );
    return options;
}

function onChampsLexicaux() {
    let span = document.createElement("span");
    span.className = "input-group-text";
    span.id = "champLexical" + ++champsLexicauxNbr + "span";
    span.innerText = "Champ lexical";
    let div = document.createElement("div");
    div.className = "input-group-prepend";
    div.appendChild(span);
    let input = document.createElement("input");
    input.className = "form-control champsLexicaux enterDontSubmit";
    input.setAttribute('list', "champLexicalOptions");
    input.setAttribute('name', 'champsLexicaux[]');
    input.setAttribute('maxlength', maxContent);
    input.id = "champLexical" + champsLexicauxNbr;
    let mainDiv = document.createElement("div");
    mainDiv.className = "input-group";

    mainDiv.append(div);
    mainDiv.append(input);
    divChampsLexicaux.appendChild(mainDiv);

    latestChampLexical = document.getElementById("champLexical" + champsLexicauxNbr);
    latestChampLexical.addEventListener("keyup", onWordInput);
    latestChampLexical.addEventListener("change", onWordInput);
}

function onPeriodes() {
    let span = document.createElement("span");
    span.className = "input-group-text";
    span.id = "periode" + ++periodesNbr + "span";
    span.innerText = "Période";
    let div = document.createElement("div");
    div.className = "input-group-prepend";
    div.appendChild(span);
    let input = document.createElement("input");
    input.className = "form-control periodes enterDontSubmit";
    input.setAttribute('list', "periodeOptions");
    input.setAttribute('name', 'periodes[]');
    input.setAttribute('maxlength', maxContent);
    input.id = "periode" + periodesNbr;
    let mainDiv = document.createElement("div");
    mainDiv.className = "input-group";

    mainDiv.append(div);
    mainDiv.append(input);
    divPeriodes.appendChild(mainDiv);

    latestPeriode = document.getElementById("periode" + periodesNbr);
    latestPeriode.addEventListener("keyup", onWordInput);
    latestPeriode.addEventListener("change", onWordInput);
}

function onSiecles() {
    let span = document.createElement("span");
    span.className = "input-group-text";
    span.id = "siecle" + ++sieclesNbr + "span";
    span.innerText = "Siècle";
    let div = document.createElement("div");
    div.className = "input-group-prepend";
    div.appendChild(span);
    let input = document.createElement("input");
    input.className = "form-control siecles enterDontSubmit";
    input.setAttribute('list', "siecleOptions");
    input.setAttribute('name', 'siecles[]');
    input.setAttribute('maxlength', maxContent);
    input.setAttribute("type", "number");
    input.id = "siecle" + sieclesNbr;
    let mainDiv = document.createElement("div");
    mainDiv.className = "input-group";

    mainDiv.append(div);
    mainDiv.append(input);
    divSiecles.appendChild(mainDiv);

    latestSiecle = document.getElementById("siecle" + sieclesNbr);
    latestSiecle.addEventListener("keyup", onWordInput);
    latestSiecle.addEventListener("change", onWordInput);
}

function onSynonyme() {
    let span = document.createElement("span");
    span.className = "input-group-text";
    span.id = "synonyme" + ++synonymesNbr + "span";
    span.innerText = "Synonyme";
    let div = document.createElement("div");
    div.className = "input-group-prepend";
    div.appendChild(span);
    let input = document.createElement("input");
    input.className = "form-control synonymes enterDontSubmit";
    input.setAttribute('list', "synonymeOptions");
    input.setAttribute('name', 'synonymes[]');
    input.setAttribute('maxlength', maxContent);
    input.id = "synonyme" + synonymesNbr;
    let mainDiv = document.createElement("div");
    mainDiv.className = "input-group";

    mainDiv.append(div);
    mainDiv.append(input);
    divSynonymes.appendChild(mainDiv);

    latestSynonyme = document.getElementById("synonyme" + synonymesNbr);
    latestSynonyme.addEventListener("keyup", onWordInput);
    latestSynonyme.addEventListener("change", onWordInput);
}

function onAntonyme() {
    let span = document.createElement("span");
    span.className = "input-group-text";
    span.id = "antonyme" + ++antonymesNbr + "span";
    span.innerText = "Antonyme";
    let div = document.createElement("div");
    div.className = "input-group-prepend";
    div.appendChild(span);
    let input = document.createElement("input");
    input.className = "form-control antonymes enterDontSubmit";
    input.setAttribute('list', "antonymeOptions");
    input.setAttribute('name', 'antonymes[]');
    input.setAttribute('maxlength', maxContent);
    input.id = "antonyme" + antonymesNbr;
    let mainDiv = document.createElement("div");
    mainDiv.className = "input-group";

    mainDiv.append(div);
    mainDiv.append(input);
    divAntonymes.appendChild(mainDiv);

    latestAntonyme = document.getElementById("antonyme" + antonymesNbr);
    latestAntonyme.addEventListener("keyup", onWordInput);
    latestAntonyme.addEventListener("change", onWordInput);
}

function onVariants() {
    if (latestVariantType.value === "" || latestVariant.value === "" || filterAlreadyChosenOptions("variant").includes("") || filterAlreadyChosenOptions("variantType").includes("")) return;

    let variantTypeSpan = document.createElement("span");
    variantTypeSpan.className = "input-group-text";
    variantTypeSpan.id = "variantType" + ++variantsNbr + "span";
    variantTypeSpan.innerText = "Type";
    let variantTypeDiv = document.createElement("div");
    variantTypeDiv.className = "input-group-prepend";
    variantTypeDiv.appendChild(variantTypeSpan);
    let variantTypeInput = document.createElement("input");
    variantTypeInput.className = "form-control variantTypes enterDontSubmit";
    variantTypeInput.setAttribute('list', "variantTypesOptions");
    variantTypeInput.setAttribute('name', 'variantTypes[]');
    variantTypeInput.setAttribute('maxlength', maxContent);
    variantTypeInput.id = "variantType" + variantsNbr;
    let variantTypeMainDiv = document.createElement("div");
    variantTypeMainDiv.className = "input-group";

    variantTypeMainDiv.append(variantTypeDiv);
    variantTypeMainDiv.append(variantTypeInput);

    let variantSpan = document.createElement("span");
    variantSpan.className = "input-group-text";
    variantSpan.id = "variant" + variantsNbr + "span";
    variantSpan.innerText = "Variant";
    let variantDiv = document.createElement("div");
    variantDiv.className = "input-group-prepend";
    variantDiv.appendChild(variantSpan);
    let variantInput = document.createElement("input");
    variantInput.className = "form-control variantTypes enterDontSubmit";
    variantInput.setAttribute('name', 'variants[]');
    variantInput.setAttribute('maxlength', maxContent);
    variantInput.id = "variant" + variantsNbr;
    let variantMainDiv = document.createElement("div");
    variantMainDiv.className = "input-group";

    variantMainDiv.append(variantDiv);
    variantMainDiv.append(variantInput);


    let mainDiv = document.createElement("div");
    mainDiv.className = "col-md-4  border border-info rounded";
    mainDiv.appendChild(variantTypeMainDiv);
    mainDiv.appendChild(variantMainDiv);
    divVariants.appendChild(mainDiv);

    latestVariant = document.getElementById("variant" + variantsNbr);
    latestVariantType = document.getElementById("variantType" + variantsNbr);
    latestVariant.addEventListener("keyup", onWordInput);
    latestVariantType.addEventListener("keyup", onWordInput);
    latestVariant.addEventListener("change", onWordInput);
    latestVariantType.addEventListener("change", onWordInput);
}

function onReferences() {
    document.getElementById("refTitre" + referencesNbr++).setAttribute("required", "true");
    let rowDiv = document.createElement("div");
    rowDiv.dataset.name = "reference";
    rowDiv.className = "form-row";

    let titreSpan = document.createElement("span");
    titreSpan.className = "input-group-text";
    titreSpan.id = "refTitre" + referencesNbr + "span";
    titreSpan.innerText = "Titre";
    let titreDiv = document.createElement("div");
    titreDiv.className = "input-group-prepend";
    titreDiv.appendChild(titreSpan)
    let titreInput = document.createElement("input");
    titreInput.className = "form-control refTitres enterDontSubmit";
    titreInput.setAttribute('name', 'titres[]');
    titreInput.setAttribute('maxlength', maxContent);
    titreInput.id = "refTitre" + referencesNbr;
    let titreMainDiv = document.createElement("div");
    titreMainDiv.className = "input-group col-xl-3";
    titreMainDiv.append(titreDiv);
    titreMainDiv.append(titreInput);
    rowDiv.appendChild(titreMainDiv);

    let auteurSpan = document.createElement("span");
    auteurSpan.className = "input-group-text";
    auteurSpan.id = "refAuteur" + referencesNbr + "span";
    auteurSpan.innerText = "Auteur";
    let auteurDiv = document.createElement("div");
    auteurDiv.className = "input-group-prepend";
    auteurDiv.appendChild(auteurSpan)
    let auteurInput = document.createElement("input");
    auteurInput.className = "form-control refAuteurs enterDontSubmit";
    auteurInput.setAttribute('name', 'auteurs[]');
    auteurInput.setAttribute('maxlength', maxContent);
    auteurInput.id = "refAuteur" + referencesNbr;
    let auteurMainDiv = document.createElement("div");
    auteurMainDiv.className = "input-group col-xl-3";
    auteurMainDiv.append(auteurDiv);
    auteurMainDiv.append(auteurInput);
    rowDiv.appendChild(auteurMainDiv);

    let editeurSpan = document.createElement("span");
    editeurSpan.className = "input-group-text";
    editeurSpan.id = "refEditeur" + referencesNbr + "span";
    editeurSpan.innerText = "Editeur";
    let editeurDiv = document.createElement("div");
    editeurDiv.className = "input-group-prepend";
    editeurDiv.appendChild(editeurSpan)
    let editeurInput = document.createElement("input");
    editeurInput.className = "form-control refEditeurs enterDontSubmit";
    editeurInput.setAttribute('name', 'editeurs[]');
    editeurInput.setAttribute('maxlength', maxContent);
    editeurInput.id = "refEditeur" + referencesNbr;
    let editeurMainDiv = document.createElement("div");
    editeurMainDiv.className = "input-group col-xl-3";
    editeurMainDiv.append(editeurDiv);
    editeurMainDiv.append(editeurInput);
    rowDiv.appendChild(editeurMainDiv);

    let pageSpan = document.createElement("span");
    pageSpan.className = "input-group-text";
    pageSpan.id = "refPage" + referencesNbr + "span";
    pageSpan.innerText = "Pages";
    let pageDiv = document.createElement("div");
    pageDiv.className = "input-group-prepend";
    pageDiv.appendChild(pageSpan)
    let pageInput = document.createElement("input");
    pageInput.className = "form-control refPages enterDontSubmit";
    pageInput.setAttribute('name', 'pages[]');
    pageInput.setAttribute('maxlength', maxContent);
    pageInput.id = "refPage" + referencesNbr;
    let pageMainDiv = document.createElement("div");
    pageMainDiv.className = "input-group col-xl-3";
    pageMainDiv.append(pageDiv);
    pageMainDiv.append(pageInput);
    rowDiv.appendChild(pageMainDiv);

    let lieuEdSpan = document.createElement("span");
    lieuEdSpan.className = "input-group-text";
    lieuEdSpan.id = "refLieuEdition" + referencesNbr + "span";
    lieuEdSpan.innerText = "Lieu d'édition";
    let lieuEdDiv = document.createElement("div");
    lieuEdDiv.className = "input-group-prepend";
    lieuEdDiv.appendChild(lieuEdSpan)
    let lieuEdInput = document.createElement("input");
    lieuEdInput.className = "form-control refLieuxEdition enterDontSubmit";
    lieuEdInput.setAttribute('name', 'lieuxEdition[]');
    lieuEdInput.setAttribute('maxlength', maxContent);
    lieuEdInput.id = "refLieuEdition" + referencesNbr;
    let lieuEdMainDiv = document.createElement("div");
    lieuEdMainDiv.className = "input-group col-xl-3";
    lieuEdMainDiv.append(lieuEdDiv);
    lieuEdMainDiv.append(lieuEdInput);
    rowDiv.appendChild(lieuEdMainDiv);

    let dateEdSpan = document.createElement("span");
    dateEdSpan.className = "input-group-text";
    dateEdSpan.id = "refDateEdition" + referencesNbr + "span";
    dateEdSpan.innerText = "Date d'édition";
    let dateEdDiv = document.createElement("div");
    dateEdDiv.className = "input-group-prepend";
    dateEdDiv.appendChild(dateEdSpan)
    let dateEdInput = document.createElement("input");
    dateEdInput.className = "form-control refDatesEdition enterDontSubmit";
    dateEdInput.setAttribute('name', 'datesEdition[]');
    dateEdInput.setAttribute('maxlength', maxContent);
    dateEdInput.id = "refDateEdition" + referencesNbr;
    let dateEdMainDiv = document.createElement("div");
    dateEdMainDiv.className = "input-group col-xl-3";
    dateEdMainDiv.append(dateEdDiv);
    dateEdMainDiv.append(dateEdInput);
    rowDiv.appendChild(dateEdMainDiv);

    let lienSpan = document.createElement("span");
    lienSpan.className = "input-group-text";
    lienSpan.id = "refLien" + referencesNbr + "span";
    lienSpan.innerText = "Lien";
    let lienDiv = document.createElement("div");
    lienDiv.className = "input-group-prepend";
    lienDiv.appendChild(lienSpan)
    let lienInput = document.createElement("input");
    lienInput.className = "form-control refLiens enterDontSubmit";
    lienInput.setAttribute('name', 'liens[]');
    lienInput.setAttribute('maxlength', maxContent);
    lienInput.id = "refLien" + referencesNbr;
    let lienMainDiv = document.createElement("div");
    lienMainDiv.className = "input-group col-xl-3";
    lienMainDiv.append(lienDiv);
    lienMainDiv.append(lienInput);
    rowDiv.appendChild(lienMainDiv);

    let fileSpan = document.createElement("span");
    fileSpan.className = "input-group-text";
    fileSpan.id = "refFile" + referencesNbr + "span";
    fileSpan.innerText = "Référence";
    let fileDiv = document.createElement("div");
    fileDiv.className = "input-group-prepend";
    fileDiv.appendChild(fileSpan)
    let fileInput = document.createElement("input");
    fileInput.className = "form-control refFiles";
    fileInput.setAttribute('name', 'references[]');
    fileInput.id = "refFile" + referencesNbr;
    fileInput.type = "file"
    let fileMainDiv = document.createElement("div");
    fileMainDiv.className = "input-group col-xl-3";
    fileMainDiv.append(fileDiv);
    fileMainDiv.append(fileInput);
    rowDiv.appendChild(fileMainDiv);

    divReferences.append(document.createElement("br"))
    divReferences.appendChild(rowDiv);

    latestReferenceFile.removeEventListener("change", onWordInput);
    latestReferenceLien.removeEventListener("keyup", onWordInput);
    latestReferenceDateEd.removeEventListener("keyup", onWordInput);
    latestReferenceLieuEd.removeEventListener("keyup", onWordInput);
    latestReferencePage.removeEventListener("keyup", onWordInput);
    latestReferenceEditeur.removeEventListener("keyup", onWordInput);
    latestReferenceAuteur.removeEventListener("keyup", onWordInput);
    latestReferenceTitre.removeEventListener("keyup", onWordInput);
    latestReferenceFile = document.getElementById("refFile" + referencesNbr);
    latestReferenceLien = document.getElementById("refLien" + referencesNbr);
    latestReferenceDateEd = document.getElementById("refDateEdition" + referencesNbr);
    latestReferenceLieuEd = document.getElementById("refLieuEdition" + referencesNbr);
    latestReferencePage = document.getElementById("refPage" + referencesNbr);
    latestReferenceEditeur = document.getElementById("refEditeur" + referencesNbr);
    latestReferenceAuteur = document.getElementById("refAuteur" + referencesNbr);
    latestReferenceTitre = document.getElementById("refTitre" + referencesNbr);
    addEventListenerOnAllFieldsOfTheLatestReference();
}

const onWordInput = (e) => {
    if (e.keyCode === 40 || e.keyCode === 38) return; // Make possible to search trough the propositions
    let targetName = e.target.parentElement.parentElement.dataset.name ? e.target.parentElement.parentElement.dataset.name : e.target.parentElement.parentElement.parentElement.dataset.name;

    if (targetName !== "reference" && targetName !== "variant") {
        let alreadyChosenOptions = filterAlreadyChosenOptions(targetName);

        document.getElementById(targetName + "Options").innerHTML = filterNewOptions(alreadyChosenOptions, targetName);

        if (e.target.value === "" || alreadyChosenOptions.includes("")) return;
    }

    switch (targetName) {
        case "antonyme" :
            onAntonyme();
            break;
        case "synonyme":
            onSynonyme();
            break;
        case "periode":
            onPeriodes();
            break;
        case "champLexical":
            onChampsLexicaux();
            break;
        case "siecle":
            onSiecles();
            break;
        case "reference":
            onReferences();
            break;
        case "variant":
            onVariants();
            break;
    }
}
latestAntonyme.addEventListener("keyup", onWordInput);
latestAntonyme.addEventListener("change", onWordInput);

latestSynonyme.addEventListener("keyup", onWordInput);
latestSynonyme.addEventListener("change", onWordInput);

latestPeriode.addEventListener("keyup", onWordInput);
latestPeriode.addEventListener("change", onWordInput);

latestChampLexical.addEventListener("keyup", onWordInput);
latestChampLexical.addEventListener("change", onWordInput);

latestSiecle.addEventListener("keyup", onWordInput);
latestSiecle.addEventListener("change", onWordInput);

latestVariant.addEventListener("keyup", onWordInput);
latestVariantType.addEventListener("keyup", onWordInput);
latestVariant.addEventListener("change", onWordInput);
latestVariantType.addEventListener("change", onWordInput);

addEventListenerOnAllFieldsOfTheLatestReference();

document.getElementById('libelleInput').addEventListener("change", (e) => {
    //TODO A bit simple, improvement necessary
    if (words.includes(e.target.value)) {
        alert("Le mot existe déjà dans la base de données");
        submitBtn.disabled = true;
        submitBtn.innerText = "Le libellé choisi existe déjà dans la base de données";
    } else {
        submitBtn.disabled = false;
        submitBtn.innerText = "Valider";
    }
})

/** edition references **/
let existingReferences = document.getElementsByClassName("existingReferences");
let latestClickedDeleteBtn;

Array.from(existingReferences).forEach(elem => {
    elem.addEventListener('click', (e) => {
        // Since multiple elements are in the aimed section, wee seek if the target has a dataset, if not, its parent, if not, its grandparent
        // manual tests showed that the grandparent is the maximum needed to be reached
        let referenceId = getIdOnDataSet(e.target);

        //Reinitializing the counter and text of the deletion button of the latest visited reference where the button was pressed
        if (clicksOnDeleteReferenceBtn !== 0) {
            clicksOnDeleteReferenceBtn = 0;
            latestClickedDeleteBtn.innerText = "Supprimer la référence";
        }

        document.getElementById("modalRef" + referenceId).click()
    })
})

function getIdOnDataSet(elem){
    if (elem.dataset.id)
        return elem.dataset.id;
    return getIdOnDataSet(elem.parentNode);
}


function onDeleteReferenceBtn(e) {
    if (latestClickedDeleteBtn !== e.target) {
        latestClickedDeleteBtn = e.target;
    }
    switch (clicksOnDeleteReferenceBtn++) {
        case 0:
            e.target.innerText = "Supprimer la référence\nEncore une fois pour être sûr 🤔";
            break;
        case 1:
            e.target.innerText = "Supprimer la référence\nUne dernière fois pour être certain 😏";
            break;
        case 2:
            let input = document.createElement("input");
            input.setAttribute('name', 'deleteReference');
            input.setAttribute("hidden", "true");
            e.target.parentNode.appendChild(input);
            document.getElementById("confirmRef" + e.target.dataset.id).click();
            break;
    }
}

Array.from(document.getElementsByClassName("deleteReferenceBtn")).forEach(e => e.addEventListener("click", onDeleteReferenceBtn));

/** Illustration type controller **/

document.getElementById("illustration").addEventListener("change", (e) => {
    if (!e.target.value.toLowerCase().endsWith(".jpg") && !e.target.value.toLowerCase().endsWith(".jpeg")
        && !e.target.value.toLowerCase().endsWith(".png") && !e.target.value.toLowerCase().endsWith(".gif"))
        alert("Le fichier ne semble pas être un .jpg, .jpeg, .png ou .gif\nIl sera potentiellement(quasi certainement en fait) ignoré.")
});