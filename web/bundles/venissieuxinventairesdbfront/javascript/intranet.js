$(document).ready(function () {
    $('#listeResultatsArticles').DataTable({
        //Afficher le champ de recherche
        searching: true,
        //Ne pas afficher le nombre de lignes par page
        bLengthChange: false,
        processing: true,
        serverSide: true,
        ajax: "./paginer",
        sAjaxDataProp: "data",
        pageLength: 10,
        columns: [
            {"data": "id"},
            {"data": "nom"},
            {"data": "categorie"},
            {"data": "date_achat"},
            {"data": "statut"},
            {"data": "etat"},
            {"data": "commentaire"},
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
});












