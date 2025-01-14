const filterButton = document.getElementById("show-filters");
const filtriAggiuntivi = Array.from(document.getElementsByClassName("filtro-aggiuntivo"));

if (filterButton) {
    filterButton.onclick = () => {
        filtriAggiuntivi.forEach((element) => {
            element.classList.toggle("filtro-aggiuntivo");
        })
        return false;
    }
}

function slider(){
    const prev = document.querySelector(".prev");
    const next = document.querySelector(".next");
    const cCont = document.querySelector(".slideshow");

    prev.addEventListener("click",()=>{
        cCont.scrollBy(-200,0);
    });

    next.addEventListener("click",()=>{
        cCont.scrollBy(200,0);
    });
}

// ===== CONTROLLI FORM ======
function validazioneCaricaNft() {
    const input = document.getElementById("descrizione");

    let node;
    let p = input.parentNode.previousSibling;
    if (p.className == "messaggi-form") {
        p.remove();
        p = input.parentNode.previousSibling;
    }

    let desc = input.value;
    if (desc.includes(".")) {
        let desc = desc.split(".");
        let alt = desc[0];
        if (alt.length > 100) {
            node = document.createElement("p");
            node.className = "messaggi-form";
            node.appendChild(document.createTextNode("La prima frase del campo descrizione (dall'inizio fino al primo punto) deve essere lunga al massimo 150 caratteri!"));
            p.after(node);
            return false;
        }
    }
    else {
        node = document.createElement("p");
        node.className = "messaggi-form";
        node.appendChild(document.createTextNode("Il campo descrizione deve avere almeno un punto alla fine!"));
        p.after(node);
        return false;
    }


}
