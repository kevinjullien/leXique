"use strict"

document.onreadystatechange = function () {
    if (document.readyState === "complete") {
        let searchbar = document.getElementById('searchBar');
        let tableRowList = document.getElementById("table").getElementsByTagName("tr");
        searchbar.addEventListener("keyup", () => {
            let val = searchbar.value.toLowerCase();
            Array.from(tableRowList).forEach((tr) => {
                if(tr.rowIndex !== 0 && tr.className !== "letterDivision") {
                let found = false;
                let column = 0;
                let tdList = tr.getElementsByTagName("td")
                Array.from(tdList).forEach((td) => {
                    if (column < tdList.length - 1) { // does not check the last column
                        if (td.innerText.toLowerCase().includes(val)) found = true;
                    }
                    column++;
                })
                tr.hidden = !found;
                    }
            })
        })
    }
}