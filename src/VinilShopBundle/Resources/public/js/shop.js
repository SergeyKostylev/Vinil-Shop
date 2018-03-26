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

    var $orderSelect = $('.order-select');
    var $orderSelectSpiner = $('.order-select-spiner');
    $orderSelectSpiner.remove();
    $orderSelect.fadeIn(300);

    $orderSelect.change(function () {
        var $this = $(this);
        var $orderId = $this.siblings().filter('.order-id-data').data('order-id');
        var $status = $this.val();
        var $statufField = $this.parent().parent().siblings('.status-field');
        var $orderStateInShowPage = $('#order-state-in-show-page');

        var $iconTrue = $('#active-true-icon').clone();
        var $iconFalse = $('#active-false-icon').clone();
        var $iconQuestion = $('#active-question-icon').clone();

        $.get('/admin/odrer/status/edit/' + $orderId +'/' + $status)
            .done(function (r) {
                var $active = r.active;

                if ($('div').is('#status-icons-for-table')){
                    if($active) {
                        $statufField.text(' ').append($iconTrue.fadeIn(500));
                    }else{
                        $statufField.text(' ').append($iconFalse.fadeIn(500));
                    }
                }
                if ($('div').is('#status-icons-for-order-page')){
                    if($active) {
                        $orderStateInShowPage.text(' ').append($iconTrue.fadeIn(500));
                    }else{
                        $orderStateInShowPage.text(' ').append($iconFalse.fadeIn(500));
                    }
                }
            })
            .fail(function (r) {
                if ($('div').is('#status-icons-for-table')){
                    $statufField.text(' ').append($iconQuestion.fadeIn(500));
                }
                if ($('div').is('#status-icons-for-order-page')){
                    $orderStateInShowPage.text(' ').append($iconQuestion.fadeIn(500));
                }
            });

    });

    function  amountInCart() {

        $.get('/amount-product-in-cart')
            .done(function (r) {
                var $cartIcon = $('#cart-icon');

                $cartIcon.text(r.amount);
                $cartIcon.fadeIn();
            })
            .fail(function (r) {
                console.log(r);
            });

    }

    function  amountFeedback() {

        $.get('/amount-active-feedback')
            .done(function (r) {
                var $feedbacksIcon = $('#feedbacks-icon');

                $feedbacksIcon.text(r.amount);
                $feedbacksIcon.fadeIn();
            })
            .fail(function (r) {
                console.log(r);
            });
    }
    amountFeedback();

    var $createOrderForm = $('#create_order-form');
    var $badMessageOderCreateWindow = $('#bad-message-oder-create-window');
    var $badMessageField = $('#bad-text-message');

    $createOrderForm.on('click', function () {
        $badMessageOderCreateWindow.fadeOut(500);
    });

    var $createOrderBtn = $('#create-order-btn');
    $('#order-phone').mask("+38(999) 99-99-999");

    $createOrderBtn.on('click', function () {
        var $this = $(this);
        var $orderSenderName = $('#order-sender-name');
        var $orderEmail = $('#order-email');
        var $orderPhone = $('#order-phone');
        var $orderAddress = $('#order-address');
        var $orderDescription = $('#order-description');
        var $orderInfo = $('#order-info');

        if($orderSenderName.val().length >= 2  &&
            $orderEmail.val() &&
            $orderPhone.val().length === 18 &&
            $orderAddress.val().length >= 9 &&
            $orderInfo.is(":checked")){

            var $spiner = $('#create-order-spiner').clone();
            $spiner.fadeIn();
            $createOrderBtn.text('');
            $createOrderBtn.append($spiner);

            $.post('/api/order/create',{
                name: $orderSenderName.val(),
                email: $orderEmail.val(),
                phone: $orderPhone.val(),
                address: $orderAddress.val(),
                description: $orderDescription.val()
            },'json')
                .done(function (r) {
                    var $createOrderForm = $('#create-order-form');
                    var $goodMessageOderCreateWindow = $('#good-message-oder-create-window');
                    var $orderNumberField = $('#order-number-field');
                    $createOrderForm.fadeOut();

                    $orderNumberField.text(r.number_order);
                    setTimeout(function () {
                        $goodMessageOderCreateWindow.fadeIn(700);
                    },700);
                    console.log(r.answer);
                    amountInCart();

                })
                .fail(function (r) {
                    $spiner.remove();
                    $createOrderBtn.text('Готово');

                    console.log(r.responseJSON.answer);
                    $badMessageField.text(r.responseJSON.answer);
                    $badMessageOderCreateWindow.fadeIn(700);
                });

        }else{
            $badMessageField.text('Неверно заполнена форма.');
            $badMessageOderCreateWindow.fadeIn(700);
        }

    });

    //////////FEEDBACK
     var $submitFeedbackBtn = $('.submit-feedback-btn');
     var $captchaBtn = $('#captcha-btn');

    $captchaBtn.on('click', function () {
       var $this = $(this);
       var $old = $this.attr('src');
       $this.attr('src', $old + '?1');

    });

    $submitFeedbackBtn.on('click', function () {

        var $feedbackForm = $('#feedback-form');

        var $email = $('#sender-email');
        var $name = $('#sender-name');
        var $title = $('#sender-message-title');
        var $message = $('#sender-message');
        var $captcha = $('#captcha');

        var $blockRequestWindows = $('.block-request-windows');
        $blockRequestWindows.find('#text-reqeust-message').text('');

        $email.on('click',function () {
            $email.parent().removeClass('has-danger');
            $blockRequestWindows.fadeOut(300);
        });
        $name.on('click',function () {
            $name.parent().removeClass('has-danger');
            $blockRequestWindows.fadeOut(300);
        });
        $title.on('click',function () {
            $title.parent().removeClass('has-danger');
        });
        $message.on('click',function () {
            $message.parent().removeClass('has-danger');
            $blockRequestWindows.fadeOut(300);
        });
        $captcha.on('click',function () {
            $captcha.parent().removeClass('has-danger');
            $blockRequestWindows.fadeOut(300);
        });

        if(!$email.val() || !$name.val() || !$title.val() || !$message.val() ||  !$captcha.val()){

        if (!$email.val())
        {
            $email.parent().addClass('has-danger');
        }
        if (!$name.val())
        {
            $name.parent().addClass('has-danger');
        }
        if (!$title.val())
        {
            $title.parent().addClass('has-danger');
        }
        if (!$message.val())
        {
            $message.parent().addClass('has-danger');
        }
        if (!$captcha.val())
        {
            $captcha.parent().addClass('has-danger');
        }
        }else{

            var $spiner = $('#feedback-spiner');
            $.post('/feedback/create',{
                    email: $email.val(),
                    name: $name.val(),
                    title: $title.val(),
                    message: $message.val(),
                    captcha: $captcha.val()
            },'json')
                .done(function (r) {
                    $blockRequestWindows.find('#bad-icon').fadeOut();

                    var $feedbackForm = $('#feedback-form');
                    $blockRequestWindows.find('#bad-icon').remove();

                    $feedbackForm.fadeOut();

                    $spiner.fadeIn();
                    $blockRequestWindows.fadeIn(700);
                    setTimeout(function () {
                        $spiner.remove();
                        $blockRequestWindows.find('#ok-icon').fadeIn();
                        $blockRequestWindows.find('#text-reqeust-message').text(r.answer);
                    },1000);

                })
                .fail(function (r) {

                    var $answer = r.responseJSON.answer;
                    var $itemBlock = r.responseJSON.itemBlock;
                    $blockRequestWindows.find('#ok-icon').fadeOut();
                    $blockRequestWindows.find('#bad-icon').fadeIn();
                    $blockRequestWindows.find('#text-reqeust-message').text($answer);
                    $captcha.val('');
                        var $old = $captchaBtn.attr('src');
                        $captchaBtn.attr('src', $old + '?1');

                    setTimeout(function () {
                        $blockRequestWindows.fadeIn(700);
                    },300);

                });

        }
    });


    var $addCartButton = $('#add-cart-button');

    $addCartButton.on('click', function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.in-cart').data('in-cart-id');

        $.get('/cart/add/product/' + $id)
            .done(function (r) {
                console.log(r.answer);
                amountInCart();
                var $inCartButton = $('#in-cart-button');
                $addCartButton.fadeOut(100);
                setTimeout(function () {
                    $inCartButton.fadeIn(1200)
                },120);
            })
            .fail(function (r) {
                console.log(r.responseJSON.answer);
            });

    });

    var $feedbackDeleteBtn = $('.feedback-delete-btn');
    $feedbackDeleteBtn.on('click', function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.feedback-id').data('feedback-id');

        $.get('/admin/feedback/delete/' + $id)
            .done(function () {
                var $row = $this.parent().parent();
                $row.fadeOut(300);
                setTimeout(
                   function () {
                       $row.remove();
                   }, 500);
            })
    });

    var $sliderFrame = $('#sliderFrame');
    $sliderFrame.fadeIn(300);
    var $mainMenuCol = $('#main-menu-col');

    function review() {
        if ($("div").is('#sliderFrame') && $(window).width() >= 768){
            $mainMenuCol.addClass('margin-top-70px');
        }
        if ($("div").is('#sliderFrame') && $(window).width() < 768){
            $mainMenuCol.removeClass('margin-top-70px');
        }
    }
    review();


    function emptyBlockView() {
        var  $emptyBlock = $('#empty-block');

        if ($(window).width() > 575){
            $emptyBlock.fadeIn();
        }
        if ($(window).width() <= 575){
            $emptyBlock.fadeOut();
        }
    }

    emptyBlockView();


    window.onresize = function (ev) {
        review();
        emptyBlockView();
    };


    var $deleteInCartUserButton = $('.delete-in-cart-user');

    $deleteInCartUserButton.on('click',function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.product-id').data('product-id');
        var $cartList = $('#cart-list');

        $.get('/cart/delete/user/product/' + $id)
            .done(function (r) {
                console.log(r.answer);
                amountInCart();
                var $sum = $('#sum');
                var $line = $this.parent().parent().parent();
                $sum.text(r.sum);
                $line.fadeOut(333);
                setTimeout(function () {
                    $line.removeClass('d-flex');
                }, 300);
                setTimeout(function () {
                    $line.remove();
                    if(!$cartList.children().length) {
                        var $sunField = $('#sun-field');
                        var $emptyCart = $('#empty-cart');
                        $sunField.remove();
                        $emptyCart.fadeIn(500);
                    }
                }, 500);
            })
            .fail(function (r) {
                console.log(r.responseJSON.answer);
            });
    });


    var $deletInCartAnonButton = $('.delete-in-cart-anon');

    $deletInCartAnonButton.on('click',function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.product-id').data('product-id');
        var $cartList = $('#cart-list');


        $.get('/cart/delete/anon/product/' + $id)
            .done(function (r) {
                amountInCart();
                console.log(r.answer);
                var $line = $this.parent().parent().parent();
                var $sum = $('#sum');
                $sum.text(r.sum);
                $line.fadeOut(333);
                setTimeout(function () {
                    $line.removeClass('d-flex');
                }, 300);

                setTimeout(function () {
                    $line.remove();
                    if(!$cartList.children().length){
                        var $sunField = $('#sun-field');
                        var $emptyCart = $('#empty-cart');
                        $sunField.remove();
                        $emptyCart.fadeIn(500);
                    }
                }, 500);
            })
            .fail(function (r) {
                console.log(r.responseJSON.answer);
            });
    });

    function  setAmountUser($this,$id, $act) {

        $.get('/cart/set-amount/user/product/' + $id + '/' + $act)
            .done(function (r) {
                amountInCart();
                var $sum = $('#sum');
                $this.siblings().filter('.amount').text(r.amount);
                $sum.text(r.sum);
            })
            .fail(function (r) {
                console.log(r.responseJSON.answer);
            });

    }

    var $plusAmountUser = $('.plus-amount-user');

    $plusAmountUser.on('click', function () {
            var $this = $(this);
            var $id = $this.siblings().filter('.product-id').data('product-id');
            var $act = 1;
            setAmountUser($this,$id,$act);

    });

    var $minusAmountUser = $('.minus-amount-user');

    $minusAmountUser.on('click', function () {
            var $this = $(this);
            var $id = $this.siblings().filter('.product-id').data('product-id');
            var $act = -1;
            setAmountUser($this,$id,$act);

    });

    function  setAmountAnon($this,$id, $act) {

        $.get('/cart/set-amount/anon/product/' + $id + '/' + $act)
            .done(function (r) {
                amountInCart();
                var $sum = $('#sum');
                $this.siblings().filter('.amount').text(r.amount);
                $sum.text(r.sum);
            })
            .fail(function (r) {
                console.log(r.responseJSON.answer);
            });

    }

    var $plusAmountAnon = $('.plus-amount-anon');

    $plusAmountAnon.on('click', function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.product-id').data('product-id');
        var $act = 1;
        setAmountAnon($this,$id,$act);

    });

    var $minusAmountAnon = $('.minus-amount-anon');

    $minusAmountAnon.on('click', function () {
        var $this = $(this);
        var $id = $this.siblings().filter('.product-id').data('product-id');
        var $act = -1;
        setAmountAnon($this,$id,$act);

    });

    amountInCart();

    var $editLastCategChek = $('.edit_last_categ_chek');
    var $editAttribList = $('.edit_attrib_list');

    if ($editLastCategChek.attr("checked") === 'checked')
    {$editAttribList.fadeIn();}

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

    var $openHighSlider = $('.little-slide-img, .main-image');
    $openHighSlider.on('click',function () {
        var $highSlider = $('.high-slider');
        $highSlider.fadeIn();
    });

    var $closeHighSliderButton = $('.close-high-slider-button');

    $closeHighSliderButton.on('click',function () {
        var $highSlider = $('.high-slider');
        $highSlider.fadeOut();
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

    var $advSlidrDelBtn = $('.adv-slidr-del-btn');

    $advSlidrDelBtn.on('click', function () {
        var $this =$(this);
        var $imageId = $this.siblings().filter('.item-id').data('item-id');
        var $imageBlock = $this.parent().parent();

        $.get('/admin/advertising/delete/' + $imageId, function (r) {})
            .done(function (r) {
                $imageBlock.fadeOut(300);
                setTimeout(function () {
                    $imageBlock.remove();
                },700);
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
                    var $message = 'Невозможно удалить категорию товары которой существуют в базе или у которой есть дочерние категории.';
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




