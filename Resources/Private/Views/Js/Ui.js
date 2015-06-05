/* 
 * CUTTER
 * Versatile Image Cutter and Processor
 * http://github.com/VolksmissionFreudenstadt/cutter
 *
 * Copyright (c) 2015 Volksmission Freudenstadt, http://www.volksmission-freudenstadt.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var cropper;

function updateCoords(c)
{
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
}


function loadTemplate(key) {
    $.getJSON('{{ baseUrl  }}ajax/load/' + key, function (data) {
        // display info
        $('#stepTitle').html(data.title);
        $('#stepIcon span').attr('class', 'glyphicon glyphicon-'+data.icon);
        $('#measurements').html(data.w+' x '+data.h);

        // additional options
        $.getJSON('{{ baseUrl  }}ajax/options/' + key, function (opts) {
            console.log(opts);
            
            var form = '';
            var i;
            for (i=0; i<opts.length; i++) {
                form = form + '<div class="form-group">'
                form = form + '<label for="' + opts[i]['key']+'">'+opts[i]['label'] + '</label> '
                        + opts[i]['form'];
                form = form + '</div>'
            }
            $('#arguments').html(form);
            for (i=0; i<opts.length; i++) {
                $('#' + opts[i]['key']).addClass('additionalArgument');
            }
        });

        // clear results message
        $('#results').html('');
        $('#results').attr('class', '');

        
        // store current template key
        $('#data-container').data('key', key);
        
        // reset cropper
        cropper.release();
        cropper.setOptions({
            aspectRatio: data.w / data.h
        });        
    });
}

function doCut() {
    var uri = '{{ baseUrl }}cut/do';
    uri = uri + '?x=' + $('#x').val();
    uri = uri + '&y=' + $('#y').val();
    uri = uri + '&w=' + $('#w').val();
    uri = uri + '&h=' + $('#h').val();
    uri = uri + '&legal=' + $('#legal').val();
    uri = uri + '&template=' + $('#data-container').data('key');
    $('.additionalArgument').each(function(){
        uri = uri + '&' + $(this).attr('id') + '=' + $(this).val();
    });
    $.getJSON(uri, function (data) {
        console.log('Received result package:');
        console.log(data);
        $('#results').show();
        if (data['result'] == 1) {
            $('#results').attr('class', 'alert alert-success');
            $('#results').html('Das Bild wurde erfolgreich zugeschnitten.');
            // force a download, if necessary
            if (data['forceDownload']) {
                $('#resultsFrame').attr('src', '{{ baseUrl }}ui/download?url=' + data['forceDownload']);
            }            
            // fade out results message
            $('#results').fadeOut( 5000, function() {
                $('#results').hide();
            });
        } else {
            $('#results').attr('class', 'alert alert-danger');
            $('#results').html('Leider ging beim Zuschneiden etwas schief.');            
        }
        
        
    });
}


$('document').ready(function(){
    $('.templateSelector').click(function(){
        loadTemplate($(this).data('template'));
    });
    
        
    cropper = $.Jcrop($('#cropbox'), {
        aspectRatio: 1 / 1,
        onSelect: updateCoords,
        boxHeight: 400
    });

    $('#customAR').hide();
    $('#measurements').show();
    $('#results').hide();
    
    $('#btnAbort').click(function(){
        window.location.href = '{{ baseUrl }}acquisition/form';
    });

    $('#btnCut').click(function(){
        doCut();
    });
});

