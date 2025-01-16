document.addEventListener("DOMContentLoaded", () => {
    const filterButton = document.getElementById("show-filters");
    const filtriAggiuntivi = Array.from(document.getElementsByClassName("filtro-aggiuntivo"));

    // Nascondi i filtri aggiuntivi all'avvio solo se lo script è caricato
    filtriAggiuntivi.forEach((element) => {
        element.style.display = "none"; // Nasconde gli elementi
    });

    if (filterButton) {
        filterButton.onclick = () => {
            const isHidden = filtriAggiuntivi[0]?.style.display === "none";
            filtriAggiuntivi.forEach((element) => {
                element.style.display = isHidden ? "" : "none"; // Mostra o nasconde
            });
            return false;
        };
    }
});

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
            return "La prima frase del campo descrizione (dall'inizio fino al primo punto) deve essere lunga al massimo 100 caratteri!";
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
