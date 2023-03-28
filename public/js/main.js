
window.onload= countAndDisplayElements()

// compteur des amis
function countAndDisplayElements() {
    var compteurAmiDiv = document.getElementById("compteur_ami");
    var nombreElements = compteurAmiDiv.children.length;
    var afficheurCompteurAmiDiv = document.getElementById("afficheur_compteur_ami");
    if (nombreElements === 0) {

        afficheurCompteurAmiDiv.textContent = '';
    } else {
        afficheurCompteurAmiDiv.textContent = nombreElements;
    }
}

// search bar des ami
const searchAmi = document.querySelector('#search_ami');

searchAmi.addEventListener('keyup', (e) => {
    const searchedLetters = e.target.value.toLowerCase();
    const ami = document.querySelectorAll('.ami');
    filterElements(searchedLetters, ami);
})

function filterElements(letters, elements) {
    for (let i = 0; i < elements.length; i++) {
        if(elements[i].textContent.toLowerCase().includes(letters)) {
            elements[i].style.display = 'flex';
        } else {
            elements[i].style.display = 'none';
        }
    }
}
// affichage/masquage de la barre de recherche des amis
function toggleSearchFriend() {
    var searchFriendDiv = document.getElementById("search_friend");
    if (searchFriendDiv.style.display === "none") {
        searchFriendDiv.style.display = "block";
    } else {
        searchFriendDiv.style.display = "none";
    }
}
// Écouteur d'événements pour détecter les clics sur le bouton
var toggleSearchFriendButton = document.getElementById("toggle_search_friend_button");
toggleSearchFriendButton.addEventListener("click", toggleSearchFriend);



// deroullement accordion des amis
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
            panel.style.maxHeight = null;

        } else {
            panel.style.maxHeight = panel.scrollHeight + 40 + "px";
            countAndDisplayElements();
        }
    });
}



// partie profil la tabs
const tabs = document.querySelector(".wrapper");
const tabButton = document.querySelectorAll(".tab-button");
const contents = document.querySelectorAll(".content");

tabs.onclick = e => {
    console.log('help');
    const id = e.target.dataset.id;
    if (id) {
        tabButton.forEach(btn => {
            btn.classList.remove("active");
        });
        e.target.classList.add("active");

        contents.forEach(content => {
            content.classList.remove("active");
        });
        const element = document.getElementById(id);
        element.classList.add("active");
    }
}

const partieTermine = document.getElementById("partieTermine");
const home = document.querySelector('[data-id="home"]');

const partieCours = document.getElementById("partieCours");
const about = document.querySelector('[data-id="about"]');

const partieAttente = document.getElementById("partieAttente");
const contact = document.querySelector('[data-id="contact"]');

partieAttente.style.display = "none";
partieCours.style.display = "none";
// home est cliquer faire dispairer partieAttente
home.addEventListener('click', () => {
    partieAttente.style.display = "none";
    partieCours.style.display = "none";
    partieTermine.style.display = "block";
})
about.addEventListener('click', () => {
    partieAttente.style.display = "none";
    partieCours.style.display = "block";
    partieTermine.style.display = "none";
})
contact.addEventListener('click', () => {
    partieAttente.style.display = "block";
    partieCours.style.display = "none";
    partieTermine.style.display = "none";
})


const victoire = document.getElementById("victoire");
const defaite = document.getElementById("defaite");
const ratio = document.getElementById("ratio");

ratio.innerHTML = Math.round((victoire.innerHTML /  defaite.innerHTML) * 100) / 100;
console.log('suceur');