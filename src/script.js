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
    const form = document.getElementById("add-nft");
    let p = form.previousSibling;
    while (p.className == "messaggi-form") {
        p.remove();
        p = form.previousSibling;
    }

    let desc = document.getElementById("descrizione").value;
    const resDesc = validaDescrizione(desc);
    if (resDesc !== true) {
        createMessageNode(p, resDesc);
    }

    return resDesc === true;
}

function validaDescrizione(desc) {
    if (desc.includes(".")) {
        desc = desc.split(".");
        let alt = desc[0];
        if (alt.length > 100) {
            return "La prima frase del campo descrizione (dall'inizio fino al primo punto) deve essere lunga al massimo 150 caratteri!";
        }
    }
    else {
        return "Il campo descrizione deve avere almeno un punto alla fine!";
    }

    return true;
}

function createMessageNode(parent, text) {
        let node = document.createElement("p");
        node.className = "messaggi-form";
        node.appendChild(document.createTextNode(text));
        parent.after(node);
}
