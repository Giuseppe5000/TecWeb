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
