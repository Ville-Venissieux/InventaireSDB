//Concerne l'affichage de la liste des articles
$(document).ready(function () {
    $('#listeResultatsArticles').DataTable({
        //Afficher le champ de recherche
        searching: true,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        //mode AJAX
        serverSide: true,
        ajax: "./paginer",
        sAjaxDataProp: "data",
        //pagination
        pageLength: 10,
        //Définition des colonnes
        columns: [
            {"data": "id", "orderable": true},
            {"data": "nom", "orderable": true},
            {"data": "categorie", "orderable": false},
            {"data": "dateAchat", "orderable": true},
            {"data": "statut", "orderable": false},
            {"data": "etat", "orderable": false},
            {"data": "commentaire", "orderable": false},
            {
             "render": function (data, type, row) { return '<a href=\"/InventaireSDB/web/app_dev.php/front/article/editer/'+row.id+'\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-31-pencil.png\" alt=\"Modifier\" /></a>&nbsp;<a href=\"/InventaireSDB/web/app_dev.php/front/article/supprimer/'+row.id+'\" onclick=\"if (window.confirm(\'Voulez-vous vraiment supprimer les données concernant l\\\'article '+row.nom+'?\')) {return true;} else {return false;}\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-17-bin.png\" alt=\"Supprimer\" /></a>'; }
            }
        ],

        //Affichage des messages en français
        language: {
            processing: "",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
            info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix: "",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucune donnée disponible dans le tableau",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            {orderable: false, targets: -1}
        ]
    });
    
    
    //Force une taille minimum permettant l'affichage de la barre de chargement
    $('#listeResultatsArticles_wrapper').css('min-height', '500px'); 
});






//Concerne l'affichage de la liste des  pour les prêts
$(document).ready(function () {
    $('#listeResultatsArticlesPrets').DataTable({
        //Afficher le champ de recherche
        searching: true,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        //mode AJAX
        serverSide: true,
        ajax: "./paginer",
        sAjaxDataProp: "data",
        //pagination
        pageLength: 10,
        //Définition des colonnes
        columns: [
            {"data": "id", "orderable": true},
            {"data": "nom", "orderable": true},
            {"data": "categorie", "orderable": false},
            {"data": "dateAchat", "orderable": true},
            {"data": "etat", "orderable": false},
            {"data": "commentaire", "orderable": false},
            {
             "render": function (data, type, row) { return '<a href=\"javascript:if (window.confirm(\'Voulez-vous vraiment supprimer les données concernant l\\\'article '+row.nom+'?\')) {return true;} else {return false;}\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-209-cart-in.png\" alt=\"Emprunter\" /></a>'; }
            }
        ],

        //Affichage des messages en français
        language: {
            processing: "",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
            info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix: "",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucune donnée disponible dans le tableau",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            {orderable: false, targets: -1}
        ]
    });
    
    
    //Force une taille minimum permettant l'affichage de la barre de chargement
    $('#listeResultatsArticlesPrets_wrapper').css('min-height', '500px'); 
});


//Concerne l'affichage de la liste des articles empruntés
$(document).ready(function () {
    $('#listeResultatsArticlesEmprunts').DataTable({
        //Afficher le champ de recherche
        searching: false,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,

        //Affichage des messages en français
        language: {
            processing: "",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
            info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix: "",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucune donnée disponible dans le tableau",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });
});












