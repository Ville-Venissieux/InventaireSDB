//Code exécuté suite au chargement d'une page 
$(document).ready(function () {


/*********************** USAGERS ****************************************/

    //Liste des usagers
    $('#listeResultatsUsagers').DataTable({
        //Afficher le champ de recherche
        searching: true,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        //pagination
        pageLength: 10,
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
    
    //Liste des prêts d'un usager (Ecran de détail d'un usager)
    $('#listePretsUsager').DataTable({
        //Afficher le champ de recherche
        searching: false,
        //Supprimer la pagination
        bPaginate: false,
        bLengthChange: false,
        bInfo: false,
        //Tri par défaut sur la date de pret
        order: [[2, "desc"]]
    });

/*********************** ARTICLES ****************************************/

    //Liste des articles
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
                "className": "dt-body-left dt-body-nowrap",
                "render": function (data, type, row) {
                    return '<a href=\"./editer/' + row.id + '\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-31-pencil.png\" alt=\"Modifier\" /></a>&nbsp;<a href=\"./supprimer/' + row.id + '\" onclick=\"if (window.confirm(\'Voulez-vous vraiment supprimer les données concernant l\\\'article ' + row.nom + '?\')) {return true;} else {return false;}\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-17-bin.png\" alt=\"Supprimer\" /></a>';
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


    //Force une hauteur minimum pour la lisibilité de l'affichage de la liste
    $('#listeResultatsArticles_wrapper').css('min-height', '500px');

    //Liste des prêts d'un article (Ecran de détail d'un article)
    $('#listePretsArticle').DataTable({
        //Afficher le champ de recherche
        searching: false,
        //Supprimer la pagination
        bPaginate: false,
        bLengthChange: false,
        bInfo: false,
        //Tri par défaut sur la date de pret
        order: [[1, "desc"]]
    });
    
    
/*********************** PRETS ****************************************/    

    //Liste des articles disponibles (Ecran des prêts)
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
                d.articlesEmpruntesInitiaux = $('#hidListeResultatsArticlesInitiaux').val();
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
            zeroRecords: "Aucun article disponible",
            emptyTable: "Aucun article disponible",
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
    
     //Force une hauteur minimum pour la lisibilité de l'affichage de la liste
    $('#listeResultatsArticlesPrets_wrapper').css('min-height', '500px');


    //Liste des articles empruntés pour un utilisateur (Ecran des prêts)
    var listeResultatsArticlesEmpruntsTable = $('#listeResultatsArticlesEmprunts').DataTable({
        //Afficher le champ de recherche
        searching: false,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        //Supression de la pagination
        bPaginate: false,
        //supression des infos de pagination
        info: false,
        //Définition des colonnes
        columns: [
            {"data": "id", "orderable": true},
            {"data": "nom", "orderable": true},
            {"data": "categorie", "orderable": false},
            {"data": "etat", "orderable": false, "className": "dt-body-left dt-body-nowrap ",
                "render": function (data, type, row) {
                    var htmlEtat = data + '&nbsp;';
                    if (row.etat != 'Moyen')
                    {
                        htmlEtat = htmlEtat + '<a href=\"javascript:\"><img src=\"/InventaireSDB/web/bundles/venissieuxinventaireSDBfront/images/glyphicons-220-circle-arrow-down.png\" class="DegraderEtatArticle" alt=\"Dégrader l\'état\" /></a>';
                    }
                    return htmlEtat;
                }
            },
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
            zeroRecords: "Aucun article en prêt",
            emptyTable: "Aucun article en prêt",
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




    //Initialise la valeur des champs cachés contenant les id des articles empruntés initialement (au moment du chargement de la page) ainsi que ceux présents en temps réel dans la liste listeResultatsArticlesEmprunts
    listeResultatsArticlesEmpruntsTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
        var data = this.data();
        $('#hidListeResultatsArticlesInitiaux').val($('#hidListeResultatsArticlesInitiaux').val() + data.id + ';');
        $('#hidListeResultatsArticlesEmprunts').val($('#hidListeResultatsArticlesEmprunts').val() + data.id + ';');
        
    });


  

//Déplacer une ligne de la liste des emprunts(listeResultatsArticlesEmprunts) vers la liste des articles disponibles (listeResultatsArticlesPrets)
    $('#listeResultatsArticlesEmprunts tbody').on('click', 'img.TransferArticle', function () {
        
        var row = listeResultatsArticlesEmpruntsTable.row($(this).parents('tr'));
        
        //On retire l'id de l'article du champ caché
        var hidEmpruntsValue = $('#hidListeResultatsArticlesEmprunts').val();
        if (hidEmpruntsValue.indexOf(';' + row.data().id + ';') > -1)
        {
            $('#hidListeResultatsArticlesEmprunts').val(hidEmpruntsValue.replace(';' + row.data().id + ';', ';'));
        } else
        {
            $('#hidListeResultatsArticlesEmprunts').val(hidEmpruntsValue.replace(row.data().id + ';', ''));
        }

        //Suppression de la ligne dans la liste des emprunts
        row.remove().draw();

        //Rechargement (AJAX) de la liste des articles disponibles en conservant la pagination
        listeResultatsArticlesPretsTable.draw(false);

    });


//Déplacer une ligne de la liste des articles disponibles (listeResultatsArticlesPrets) vers la liste des emprunts(listeResultatsArticlesEmprunts)
    $('#listeResultatsArticlesPrets tbody').on('click', 'img.TransferEmprunt', function () {
        
        var row = listeResultatsArticlesPretsTable.row($(this).parents('tr'));
        var rowNode = row.node();

        //On enregistre l'id de l'article concerné dans un champ caché
        $('#hidListeResultatsArticlesEmprunts').val($('#hidListeResultatsArticlesEmprunts').val() + row.data().id + ';');

        //Suppression de la ligne dans la liste des articles disponibles
        row.remove();

        //Ajout de la ligne dans la liste des emprunts
        listeResultatsArticlesEmpruntsTable
                .row.add(rowNode)
                .draw();

        //Réaffichage de la liste des articles disponibles en conservant la pagination
        listeResultatsArticlesPretsTable.draw(false);

    });


    //Lorsque l'utilisateur clique sur le bouton afin d'indiquer la dégradation de l'état d'un article
    $('#listeResultatsArticlesEmprunts tbody').on('click', 'img.DegraderEtatArticle', function () {

        var row = listeResultatsArticlesEmpruntsTable.row($(this).parents('tr'));

        $.ajax({
            url: './modifierEtat', 
            type: 'GET', 
            dataType: 'text', 
            // indique l'article dont l'état est à modifier et le type de modification (dégradation)
            data: {idArticle: row.data().id, ameliorer: false}, 
            success: function (nouvelEtat, statut) {
                //Suite à l'enregistrement côté serveur, la cellule contenant l'état de l'article est mise à jour
                listeResultatsArticlesEmpruntsTable.cell(row, 3).data(nouvelEtat).draw();
            }
        });
    });

/*********************** EDITIONS ****************************************/ 

    //Ajout des libellés de choix Tous sur les listes déroulantes des éditions
    $('#edition_usager>option[value=\'\']').html('Tous');
    $('#edition_categorie>option[value=\'\']').html('Tous');

    //La liste déroulante des usagers est désactivée par défaut
    $('#edition_usager').prop('disabled', true);

    //En fonction des choix de disponibilité, la liste déroulante des usagers est activée ou désactivée
    $('#edition_disponible').change(function () {
        if ($('#edition_disponible option:selected').val() == '3')
        {
            $('#edition_usager').prop('disabled', false);
        } else
        {
            $('#edition_usager').prop('disabled', true);
            $('#edition_usager').val('');
        }
    });




});


