/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './admin_styles/admin-style.scss';
import '../node_modules/jquery-confirm/css/jquery-confirm.css';
import '../node_modules/cropperjs/src/css/cropper.scss';
import '../node_modules/dropzone/dist/dropzone.css';
import './admin_styles/bootstrap-datepicker.css';
require('bootstrap');

const $ = require('jquery');
global.$ = global.jQuery = $;
require('jquery-confirm');
require('cropperjs');
require('jquery-cropper');
const { Dropzone } = require('dropzone');
import './bootstrap-datepicker.js';
import './bootstrap-datepicker.fr.min.js';


$(document).ready(function(){
    $('.deletelink').each(function(){
        $(this).on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.confirm({
                title: 'Supprimer',
                content: 'Voulez vous vraiment supprimer cet élément ?',
                buttons: {
                    confirm: {
                        text : 'Confirmer',
                        action: function(){
                            window.location = url;
                        }
                    },
                    cancel: {
                        text : 'Annuler'
                    }
                }
            });
        });
    });
    $('.datepicker').datepicker({
        format: "dd-mm-yyyy",
        language: "fr",
        weekStart: 1
    });
    if($("#mydropzone").length){
        const dropzone = new Dropzone("#mydropzone", { 
            paramName: "file",
            maxFilesize: 5,
            dictDefaultMessage: "Glissez vos fichiers ici",
            processing: function(file){
                $('#form_edit_gallery').prop('disabled', true);
            },
            queuecomplete: function(){
                $('#form_edit_images').css('visibility', 'visible');
                $('#form_edit_gallery').prop('disabled', false);
            }});
    }
    
    var $cropzone = $('#cropzone');
    var $coordinations = $('.crop-coordinations');
    var $ratio = $('#cropper_ratio');
    var start_coord = false;
    if($coordinations.val()){
        var coord = $coordinations.val();
        var coord_array = coord.split(',');
        var start_coord = {x: parseInt(coord_array[0]), y: parseInt(coord_array[1]), width: parseInt(coord_array[2]), height: parseInt(coord_array[3])};
    }
    $cropzone.cropper({
        viewMode: 1,
        aspectRatio: $ratio.val(),
        background: false,
        rotatable: false,
        scalable: false,
        zoomable: false,
        autoCropArea: 1,
        crop: function(event) {
            $coordinations.val(Math.round(event.detail.x) + ',' + Math.round(event.detail.y) + ',' + Math.round(event.detail.width) + ',' + Math.round(event.detail.height));
        },
        ready() {
            if (start_coord){
                this.cropper.setData(start_coord);
            }
        }
    });
});