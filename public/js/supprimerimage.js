window.onload = () => {
    // Gestion des boutons "Supprimer"

    let links = document.querySelectorAll("[data-delete]")

    // On boucle sur links
    for (link of links) {
        //1.récupérer le click sur le bouton supprimer
        //2. récueper dans le balise a le token
        //3.envoyer la requête à /users/images/supprimer/{id}
        //4.une fois réponse eu -> suppression de l'image
        link.addEventListener("click", function (e) {
            // Désactiver le lien href - empêcher la navigation
            e.preventDefault()

            // On demande confirmation
            if (confirm("Voulez-vous supprimer cette image ?")) {
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE

                // fetch = permet d'envoyer une requete ajax sous forme de "promesse" pour lui dire ce qu'il doit faire (avec .then) si la promesse est tenue et qu'il reçoit une réponse.
                // header = infos envoyé en en-tête = Le header, ou en-tête / entête d'un fichier informatique ou d'un paquet transitant sur un réseau informatique, contient les données présentes au début de ce fichier ou du paquet. En transmission de données, les données qui suivent le header sont souvent appelées charge utile ou body. Il contient les informations nécessaires à l'entité homologue distante pour extraire et traiter les données.
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    //this = href //dataset = tous les attributs commençant par data // token = celui s'appelant token data-token
                    body: JSON.stringify({ "_token": this.dataset.token })

                    // RESUME : fetch nous permet d'envoyer à l'url (qui est dans le href), en méthode DELETEn et en utilisant du json avec XMLHttpRequest, le token. /supprime/image/{id} methods={"DELETE"} -> $image->getId(), $data['_token']

                    //.then = quand le promesse est tenue alors... (syntaxe : variable=>fonction )
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()

                    // Données en mains
                ).then(data => {
                    //est-ce data contient success, si oui, supprime l'élément parent du lien de la balise a = supprime la div entière sinon erreur 
                    if (data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}