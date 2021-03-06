<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Guida TV di Alessandro D'Orazio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-B9BoFFAuBaCfqw6lxWBZrhg/z4NkwqdBci+E+Sc2XlK/Rz25RYn8Fetb+Aw5irxa" crossorigin="anonymous">
</head>
<body>

    <div class="modal fade" id="apriDettaglioProgramma" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNomeProgramma"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0"><small id="modalGenereProgramma"></small></p>
                <p class="mb-0" id="modalSerieProgramma"></p>
                <p class="mb-0"><span id="modalNumeroStagioneProgramma"></span> <span id="modalNumeroPuntataProgramma"></span></p>
                <img src='#' alt='modalImmagineProgramma' class='img-fluid' id="modalImmagineProgramma" />
                <p class="lead small mt-2" id="modalDescrizioneProgramma"></p>
                <p id="modalLinkApprofondimentoProgramma"></p>
                <p id="modalPalinsestoProgramma"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
            </div>
        </div>
    </div>

    <div class="jumbotron">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="{{URL::to('/')}}/img/logo.png" alt="Logo guida tv" class="img-fluid mb-3" style="max-width: 200px" />
                <h1>La guida TV per eccellenza</h1>
                <p class="lead">Made with <i class="fal fa-heart" style="color: red"></i> by D'Orazio Alessandro</p>

                <p>Per iniziare, <strong>seleziona un canale</strong></p>
                <div class="form-group">
                    <select name="canale_id" id="canale_id" onchange="getPalinsestoOdierno">
                        <option value="" disabled>Seleziona un canale...</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <img src="{{URL::to('/')}}/img/home_img.png" alt="Immagine nell'hero" class="img-fluid">
            </div>
        </div>
    </div>

    <div class="container" id="palinsesto">
        <div class="row" id="palinsesto_row">

        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/locale/it.min.js"></script>
    <script>
        $(document).ready(function(){
            $.get('{{URL::to('/')}}/api/canali', function(data, status) {
                data.forEach(function(canale) {
                    $("#canale_id").append(new Option(canale.nome, canale.id));
                });
            });

            $('#canale_id').on('change', function() {
                getPalinsestoOdierno( this.value );
            });

        });


        function apriDettaglioProgramma(id) {
            $.get('{{URL::to('/')}}/api/programmi/' + id, function(data, status) {

                $("#modalNomeProgramma").html(data.nome);

                if(data.descrizione) 
                {
                    $("#modalDescrizioneProgramma").html(data.descrizione);
                } else {
                    $("#modalDescrizioneProgramma").html('');
                }

                if(data.link_approfondimento) 
                {
                    $("#modalLinkApprofondimentoProgramma").html('<a href="' + data.link_approfondimento + '">Link approfondimento</a>');
                } else {
                    $("#modalLinkApprofondimentoProgramma").html('');
                }

                if(data.numero_stagione) 
                {
                    $("#modalNumeroStagioneProgramma").html('Stagione ' + data.numero_stagione);
                }
                else {
                    $("#modalNumeroStagioneProgramma").html('');
                }

                if(data.numero_puntata) 
                {
                    $("#modalNumeroPuntataProgramma").html('Puntata ' + data.numero_puntata);
                } else {
                    $("#modalNumeroPuntataProgramma").html('');
                }

                if(data.genere_nome)
                {
                    $("#modalGenereProgramma").html('Genere: ' + data.genere_nome);
                } else {
                    $("#modalGenereProgramma").html('');
                }

                if(data.serie_nome)
                {
                    $("#modalSerieProgramma").html('Serie: ' + data.serie_nome);
                } else {
                    $("#modalSerieProgramma").html('');
                }

                if(data.immagine) {
                    $("#modalImmagineProgramma").attr('src', data.immagine);
                } else {
                    $("#modalImmagineProgramma").attr('src', '#');
                }
                if(data.palinsesto) {
                    oraInizio = moment(data.palinsesto[0].pivot.ora_inizio, 'YYYY-MM-DD HH:mm:ss');
                    $("#modalPalinsestoProgramma").html('<i class="fad fa-tv-retro text-danger fa-fw"></i>&nbsp;' + data.palinsesto[0].nome + ', il giorno ' + oraInizio.format('DD/MM/YYYY') + ' alle ore ' + oraInizio.format('HH:mm'));
                } else {
                    $("#modalPalinsestoProgramma").html('');
                }
            });
            $('#apriDettaglioProgramma').modal('show');
        }

        function getPalinsestoOdierno(canale_id) {
            $("#palinsesto_row").html('');
                $.get('{{URL::to('/')}}/api/canali/' + canale_id + '/palinsesto', function(data, status) {
                    data.forEach(function(programma) {
                        $("#palinsesto_row").append("<div class='col-md-4' onclick='apriDettaglioProgramma(" + programma.id + ")'><img src='" + programma.immagine + "' class='img-fluid' alt='immagine programma' /></><p class='mb-0 font-weight-bolder'>" + programma.nome + "</p><p class='lead small'>" + moment(programma.pivot.ora_inizio, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm') + "</p></div>");
                    });
                });
            }
    </script>
    </body>
</html>