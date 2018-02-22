$(document).ready(function () {

    $.errorModalWindow =function ($message) {
        var $erroMesage = $('#error_message');
        $('#overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
            function(){ // пoсле выпoлнения предъидущей aнимaции
                $('#modal_form')
                    .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
                    .animate({opacity: 1, top: '50%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз
                $erroMesage.text($message);
            });
        /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */
        $('#modal_close, #modal_close_х').click( function(){ // лoвим клик пo крестику или пoдлoжке
            $('#modal_form')
                .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                    function(){ // пoсле aнимaции
                        $(this).css('display', 'none'); // делaем ему display: none;
                        $('#overlay').fadeOut(400); // скрывaем пoдлoжку
                    }
                );
        });

    };

    var $categoryListBtn = $('.category-list-btn');
    var $categoryList = $('.category-list');
    $(document).mousedown(function () {
        if ($categoryList.is(":visible")){
        $categoryList.fadeOut(120);}
    });

    $categoryListBtn.on('click',function () {
        if ($categoryList.is(":hidden")){
        $categoryList.fadeIn(120);}

    });

    var $galleryImageToggleButton =$('.gallery-image-toggle-button');
    $galleryImageToggleButton.on('click',function () {
        var $this = $(this);
        var $boby = $this.parent().siblings().filter('.card-body');
        $boby.toggle(290);
    });

    var $galleryImageButton = $('.gallery-image-button');

    $galleryImageButton.on('click',function () {
        var $this = $(this);
        var $imageId = $this.siblings().filter('.image-id').data('image-id');
        var $imageBlock = $this.parent().parent();
        $.get('/admin/gallery-image/delete/' + $imageId, function (r) {})
            .done(function (r) {
                $imageBlock.fadeOut(700);
                setTimeout(function () {
                    $imageBlock.remove();
                },900);
            })
            .fail(function (r) {
            });
    });



    var $DelProd = $('.ajaxBtnDelProd');

    $DelProd.on('click',function () {
        var $this = $(this);

        $.get('/admin/product/delete/'+ $(this).attr('id'),function (r){})
            .done(function (r) {
                $this.parent().parent().parent().toggle(230);
            })
            .fail(function (r) {
                console.log(' NOT GoodD');
            });

    });

    var $DelCategory = $('.ajaxBtnDelCategory');

    $DelCategory.on('click',function () {
        var $this = $(this);
        $.get('/admin/category/delete/'+ $(this).attr('id'),function (r){})
            .done(function (r) {
                $this.parent().toggle(230);
            })
            .fail(function (r) {
                    var $message = 'Невозможно удалить категорию товары которой существуют в базе.';
                    $.errorModalWindow($message);

            });

    });

    var $DelMan = $('.ajaxBtnDelMan');

    $DelMan.on('click' ,function () {
        var $this = $(this);
        $.get('/admin/manufacturer/delete/'+ $(this).attr('id'),function (r){})

            .done(function (r) {
                $this.parent().parent().remove();
            })
            .fail(function (r) {
                var $prodExist = r.responseJSON.prodExist;
                if($prodExist === true) {
                    var $message = 'Невозможно удалить производителя товары которого существуют в базе.';
                    $.errorModalWindow($message);
                }
            });


    });
    var $manufDispBtn = $('.manufDispBtn');

    $manufDispBtn.on('click',function () {
        var $this = $(this);
        var $manufInput = $('.manufInput');
        var $newManufInput = $('.newManufInput');
        $newManufInput.toggle();
        $manufInput.toggle();
        if ($this.text() == 'Выбрать из имеющихся') {
            $this.text('Добавить нового');
        }else{
            $this.text('Выбрать из имеющихся');
        }
    });

        var $titleImageImputBut = $('.titleImageImputBut');

        $titleImageImputBut.on('click',function () {
            var $this = $(this);
            var $titleImageImputEdid = $('.titleImageImputEdid');//Edit form element
            var $titleImagePic = $('.titleImagePic'); //Pictures
            $titleImageImputEdid.toggle();
            $titleImagePic.toggle();
            console.log($this.text());
            if ($this.text() === 'Изменить') {
                $this.text('Отмена');
            }else{
                $this.text('Изменить');
            }
    });


        var $last_categ_chek =$('.last_categ_chek'),
            $attrib_list = $('.attrib_list');

            $last_categ_chek.change(function () {
                if(this.checked){
                    $attrib_list.show();
                }else{
                    $attrib_list.hide();
                }
            });

    var $collectionHolder;

    var $addrow = $('.addAttributeButton');
    var $newrow =$('<div></div>').append($addrow);

    $collectionHolder = $('div.collection');
    $collectionHolder.append($newrow);
    $collectionHolder.data('index',$collectionHolder.find('.row').length);
    // var index = $collectionHolder.data('index');


    removeAttribRow = function () {
      var $this =$(this);
      var index = $collectionHolder.data('index');
      var $row =$this.parent().parent().remove();
        $collectionHolder.data('index', index - 1);
    };
    $('.collection').on('click', '.delRowButton', removeAttribRow);

    $addrow.on('click',function (e) {
        e.preventDefault();
        var $amount_attributes = $('.amount_attributes').data('amount-attributes');
        addRow($collectionHolder, $newrow, $amount_attributes);
    });

        function  addRow($collectionHolder,$newrow,$amount_attributes) {
        var prototype_name = $('.attributes_name').data('prototype_name');
        var prototype_value = $('.attributes_value').data('prototype_value');
        // var $amount_attributes = $('.amount_attributes').data('amount-attributes');
        var index = $collectionHolder.data('index');
        var newFormName = prototype_name;
        var newFormValue = prototype_value;
        var $blockTemplate = $('.block-attribs-template');
        var $block =$blockTemplate.clone();

        if (index < $amount_attributes){
        newFormName = newFormName.replace(/__name__/g, index);
        newFormValue = newFormValue.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $block.removeClass('block-attribs-template')
            .find('.attrib-name')
            .append(newFormName);
        $block.removeClass('disp-none')
            .find('.attrib-value')
            .append(newFormValue);

        $newrow.before($block);
        }else{
            var $message = 'Достигнуто максимальное количество характеристик для данного типа товара.';
            $.errorModalWindow($message);
        }


    }

    var $categorySelect = $('.categorySelect');
    $categorySelect.change(function(){
        if($(this).val() == 0) {return false;}

        $.get('/admin/category/attributes/'+ $(this).val(),function (r){})
            .done(function (r) {
                var $IdsOfAttrib = [];
                $.each(r,function (index,value) {
                    $IdsOfAttrib.push(value['id']);
                });
                var $amount_attributes = $IdsOfAttrib.length;
                var $tempIdsOfAttrib = $IdsOfAttrib.slice();
                $('.amount_attributes').data('amount-attributes' , $amount_attributes);
                var $attrCollection = $('.collection.card-body');
                $attrCollection.children().filter('.row.attribRow').remove();
                $collectionHolder.data('index', 0);
                $collectionHolder = $('div.collection');
                for (i = 0; i < $amount_attributes; i++) {

                    var $curentAttr = $tempIdsOfAttrib.shift();
                    addRow($collectionHolder, $newrow, $amount_attributes);
                    var $options = $attrCollection
                        .children()
                        .filter('.row.attribRow').last().children().first().children()
                        .filter('.form-group.attrib-name').children()
                        .children()
                    ;

                    $options.each(function($i){
                        var $value = parseInt($(this).val());

                        if($curentAttr == $value ) {

                            $(this).prop('selected', true);
                        }

                        if( !isNaN($value)&& $.inArray($value,$IdsOfAttrib) == -1){
                            $(this).remove();
                        }

                    });
                    var $modifiedOptions = $attrCollection
                        .children()
                        .filter('.row.attribRow').last().children().first().children()
                        .filter('.form-group.attrib-name').children()
                        .children()
                    ;
                }

            })
            .fail(function (r) {
                var $message = 'Категория не найдена';
                $.errorModalWindow($message);
            });

    });


    var $menuadd = $('#menuadd');
    var $clikTime =true;

    $menuadd.on('click',function() {
        var $menuAddBorder =$('#menuAddBorder');
        var $menuAddItems = $('.menuAddItem');

        if($clikTime){
        $menuAddItems.toggle(300);
        $clikTime = false;
        }
        setTimeout(function() {
            console.log($menuAddItems.is(":visible"));
            if($menuAddItems.is(":visible"))
            {
                $menuadd.addClass('activBg');
                $menuAddBorder.addClass('menuAddBorder');
            }else{
                $menuadd.removeClass('activBg');
                $menuAddBorder.removeClass('menuAddBorder');
            }


            $clikTime=true;
        }, 330);








    });
});




