let alphabetLinks = document.getElementById("alphabetLinks")
Array.from(document.getElementsByClassName("letterDivision")).forEach(e => {
    let link = document.createElement("a")
    link.innerText = e.id
    link.href = "#" + e.id
    alphabetLinks.appendChild(link)
    alphabetLinks.appendChild(document.createElement("br"))
})

Array.from(alphabetLinks.getElementsByTagName("a")).forEach(element => {
    element.addEventListener("click", (event) => {
        event.preventDefault();
        let o =  $( $(element).attr("href") ).offset();
        let sT = o.top - $("#header").outerHeight(true);
        window.scrollTo(0,sT);
    })
})

