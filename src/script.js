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
    removeOldMessages(form);

    const desc = document.getElementById("descrizione").value;
    const resDesc = validaDescrizione(desc);
    if (resDesc !== true) {
        createMessageNode(form.previousSibling, resDesc);
    }

    const nome = document.getElementById("nome").value;
    const resNome = validaNome(nome);
    if (resNome !== true) {
        createMessageNode(form.previousSibling, "Il campo nome può contenere solo lettere e numeri!");
    }

    return resDesc === true && resNome === true;
}

function validazioneAccedi() {
    let username = document.getElementById("username");
    let password = document.getElementById("password");
    removeOldMessages(password);
    removeOldMessages(username);

    const resUsername = validaNome(username.value);
    if (resUsername !== true) {
        createMessageNode(username.previousSibling, "Il campo username può contenere solo lettere e numeri!");
    }

    const resPassword = validaPassword(password.value);
    if (resPassword !== true) {
        createMessageNode(password.previousSibling, "Il campo password può contenere solo lettere, numeri e i seguenti caratteri speciali: ! @ # $");
    }

    return resUsername === true && resPassword === true;
}

function validazioneRegistrati() {
    let username = document.getElementById("username");
    let password = document.getElementById("password");
    let confermaPassword = document.getElementById("confirm-password");
    removeOldMessages(password);
    removeOldMessages(username);

    const resUsername = validaNome(username.value);
    if (resUsername !== true) {
        createMessageNode(username.previousSibling, "Il campo username può contenere solo lettere e numeri!");
    }

    const resPasswdAndConfermaPasswd = validaPasswordAndConfermaPassword(password.value, confermaPassword.value);
    if (resPasswdAndConfermaPasswd !== true) {
        createMessageNode(password.previousSibling, "Il campi password e ripeti password possono contenere solo lettere, numeri e i seguenti caratteri speciali: ! @ # $");
    }

    const passwdEquals = password.value === confermaPassword.value;
    if (!passwdEquals)
        createMessageNode(password.previousSibling, "I campi password e ripeti password non corrispondono!");

    return resUsername === true && resPasswdAndConfermaPasswd === true && passwdEquals === true;
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

function validaNome(username) {
    const regex = /^[a-zA-Z0-9]+$/;
    return regex.test(username);
}

function validaPassword(password) {
    const regex = /^[a-zA-Z0-9!@#$]*$/;
    return regex.test(password);
}

function validaPasswordAndConfermaPassword(password, confermaPassword) {
    const regex = /^[a-zA-Z0-9!@#$]*$/;
    if (!regex.test(password) || !regex.test(confermaPassword))
        return false;
    return true;
}

function createMessageNode(parent, text) {
        let node = document.createElement("p");
        node.className = "messaggi-form";
        node.appendChild(document.createTextNode(text));
        parent.after(node);
}

function removeOldMessages(el) {
    let p = el.previousSibling;
    while (p.className == "messaggi-form") {
        p.remove();
        p = el.previousSibling;
    }
}
