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
                "render": function (data, type, row) {
                    return '<a href=\"/InventaireSDB/web/app_dev.php/front/article/editer/' + row.id + '\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-31-pencil.png\" alt=\"Modifier\" /></a>&nbsp;<a href=\"/InventaireSDB/web/app_dev.php/front/article/supprimer/' + row.id + '\" onclick=\"if (window.confirm(\'Voulez-vous vraiment supprimer les données concernant l\\\'article ' + row.nom + '?\')) {return true;} else {return false;}\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-17-bin.png\" alt=\"Supprimer\" /></a>';
                }
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







    //Concerne l'affichage de la liste des  pour les prêts
    var listeResultatsArticlesPretsTable = $('#listeResultatsArticlesPrets').DataTable({
        //Afficher le champ de recherche
        searching: true,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        //mode AJAX
        serverSide: true,
        ajax: {
            "url": "./paginer",
            "data": function (d) {
                d.articlesEmpruntes = $('#hidListeResultatsArticlesEmprunts').val();
            }
        },
        sAjaxDataProp: "data",
        //pagination
        pageLength: 10,
        //Définition des colonnes
        columns: [
            {"data": "id", "orderable": true},
            {"data": "nom", "orderable": true},
            {"data": "categorie", "orderable": false},
            {"data": "etat", "orderable": false},
            {"data": "commentaire", "orderable": false},
            {
                "render": function (data, type, row) {
                    return '<a href=\"javascript:\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-209-cart-in.png\" class="TransferEmprunt" alt=\"Emprunter\" /></a>';
                }
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
    

    //Concerne l'affichage de la liste des articles empruntés
    var listeResultatsArticlesEmpruntsTable = $('#listeResultatsArticlesEmprunts').DataTable({
        //Afficher le champ de recherche
        searching: false,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,

        //Supression de la pagination
        bPaginate: false,

        //Définition des colonnes
        columns: [
            {"data": "id", "orderable": true},
            {"data": "nom", "orderable": true},
            {"data": "categorie", "orderable": false},
            {"data": "etat", "orderable": false},
            {"data": "commentaire", "orderable": false},
            {
                "render": function (data, type, row) {
                    return '<a href=\"javascript:\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-210-cart-out.png\" class="TransferArticle" alt=\"Retourner\" /></a>';
                }
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
    

    //Initialise la valeur du champ caché contenant  les emprunts
    listeResultatsArticlesEmpruntsTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
        var data = this.data();
        $('#hidListeResultatsArticlesEmprunts').val($('#hidListeResultatsArticlesEmprunts').val() + data.id + ';');
    });


    //Force une taille minimum permettant l'affichage de la barre de chargement
    $('#listeResultatsArticlesPrets_wrapper').css('min-height', '500px');





//Déplacer une ligne de la table des emprunts(listeResultatsArticlesEmprunts) vers la table des articles(listeResultatsArticlesPrets)
    $('#listeResultatsArticlesEmprunts tbody').on('click', 'img.TransferArticle', function () {
        var row = listeResultatsArticlesEmpruntsTable.row($(this).parents('tr'));
        var rowNode = row.node();



        //On retire l'id de l'article du champ caché
        var hidEmpruntsValue = $('#hidListeResultatsArticlesEmprunts').val();

        if (hidEmpruntsValue.indexOf(';' + row.data().id + ';') > -1)
        {
            $('#hidListeResultatsArticlesEmprunts').val(hidEmpruntsValue.replace(';' + row.data().id + ';', ';'));
        } else
        {
            $('#hidListeResultatsArticlesEmprunts').val(hidEmpruntsValue.replace(row.data().id + ';', ''));
        }


        //Suppression de la ligne dans la liste initiale
        row.remove().draw();


    });


//Déplacer une ligne de la table des articles(listeResultatsArticlesPrets) vers la table des emprunts(listeResultatsArticlesEmprunts)
    $('#listeResultatsArticlesPrets tbody').on('click', 'img.TransferEmprunt', function () {
        var row = listeResultatsArticlesPretsTable.row($(this).parents('tr'));
        var rowNode = row.node();

        //On enregistre l'id de l'article concerné dans un champ caché
        $('#hidListeResultatsArticlesEmprunts').val($('#hidListeResultatsArticlesEmprunts').val() + row.data().id + ';');

        //Suppression de la ligne dans la liste initiale
        row.remove();

        //Ajout de la ligne dans la liste de destination
        listeResultatsArticlesEmpruntsTable
                .row.add(rowNode)
                .draw();

    });


});












