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
