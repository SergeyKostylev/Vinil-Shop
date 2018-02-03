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
        $('#modal_close, #overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
            $('#modal_form')
                .animate({opacity: 0, top: '45%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
                    function(){ // пoсле aнимaции
                        $(this).css('display', 'none'); // делaем ему display: none;
                        $('#overlay').fadeOut(400); // скрывaем пoдлoжку
                    }
                );
        });

    };

    var $DelProd = $('.ajaxBtnDelProd');

    $DelProd.on('click',function () {
        var $this = $(this);
        $.get('/admin/product/delete/'+ $(this).attr('id'),function (r){})
            .done(function (r) {
                $this.parent().parent().parent().toggle(230);
            })
            .fail(function (r) {
                console.log(' NOT GooD');
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
                console.log('NOT GooD');
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

    // var $enterAtr =$('.enterAttributes');
    //         $enterAtr.on('click', function () {
    //             var $attr = $('#vinilshopbundle_product_category');
    //             var $id = $attr.val();
    //             alert($attr.val());
    //             $.get('/admin/product/attributes/'+ $id,function (r){})
    //                 .done(function (r) {
    //
    //                 })
    //                 .fail(function (r) {
    //                     alert(r);
    //                 });
    //
    //
    //         });





    var $collectionHolder;

    var $addrow = $('.addAttributeButton');
    var $newrow =$('<div></div>').append($addrow);

    $collectionHolder = $('div.collection');
    $collectionHolder.append($newrow);
    $collectionHolder.data('index',$collectionHolder.find('.row').length);



    removeAttribRow = function () {
      var $this =$(this);
      var $row =$this.parent().parent().remove();
    };

    $('.collection').on('click', '.delRowButton', removeAttribRow);


    $addrow.on('click',function (e) {
        e.preventDefault();
        addRowForm($collectionHolder,$newrow);
    });
    function addRowForm($collectionHolder,$newrow) {

        var prototype_name = $('.attributes_name').data('prototype_name');
        var prototype_value = $('.attributes_value').data('prototype_value');
        var index = $collectionHolder.data('index');
        var newFormName = prototype_name;
        var newFormValue = prototype_value;
        var $blockTemplate = $('.block-attribs-template');
        var $block =$blockTemplate.clone();

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
    };




});




