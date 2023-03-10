
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



