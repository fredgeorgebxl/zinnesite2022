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
require('jquery-ui');
const { Sortable } = require('jquery-ui/ui/widgets/sortable');
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
            $coordinations.val(Math.floor(event.detail.x) + ',' + Math.floor(event.detail.y) + ',' + Math.floor(event.detail.width) + ',' + Math.floor(event.detail.height));
        },
        ready() {
            if (start_coord){
                this.cropper.setData(start_coord);
            }
        }
    });
    var $grid = $('#grid');
    
    if($grid.length){
        $grid.sortable({
            tolerance: "pointer",
            cursor: "move",
            handle: ".imgdrag", 
            draggable: ".image-element",
            update: function (evt){
                var item = evt.item;
                setImagesOrder();
            }
        });

        var $collectionHolder = $('div.grid');
        $collectionHolder.find('.image-element').each(function() {
            addImageDeleteLink($(this));
            addOpenFormLink($(this));
            addCloseFormLink($(this).find('.alt-title'));
        });
    }
});

function setImagesOrder(){
    var collection = $('div.grid');
    collection.find('.image-element').each(function(index){
        $(this).find('.weight').val(index + 1);
    });
}

function addImageDeleteLink(imageElement){
    var deleteLink = $('<a href="#" title="Supprimer l\'image" class="far fa-times-circle imgremove" aria-hidden="true"></a>');
    imageElement.find('.thumbnail').before(deleteLink);
    deleteLink.on('click', function(e) {
        e.preventDefault();
        $.confirm({
            title: 'Supprimer l\'image',
            content: 'Voulez-vous réellement supprimer cette image ?',
            buttons: {
                confirm: {
                    text : 'Confirmer',
                    action: function(){
                        imageElement.remove();
                    }
                },
                cancel: {
                    text : 'Annuler'                         
                }
            }
        });
    });
}

function addCloseFormLink(formelement){
    formelement.find('.closeform').on('click', function(e){
        e.preventDefault();
        formelement.css('visibility', 'hidden');
    });
}

function addOpenFormLink(imageelement){
    imageelement.find('.openform').on('click', function(e){
        e.preventDefault();
        imageelement.find('.alt-title').css('visibility', 'visible');
    });
}