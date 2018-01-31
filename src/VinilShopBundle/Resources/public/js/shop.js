$(document).ready(function () {

    var $DelProd = $('.ajaxBtnDelProd');

    $DelProd.click(function () {
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

    $DelCategory.click(function () {
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

    $DelMan.click(function () {
        var $this = $(this);
        $.get('/admin/manufacturer/delete/'+ $(this).attr('id'),function (r){})
            .done(function (r) {
                $this.parent().parent().remove();
            })
            .fail(function (r) {
                console.log('NOT GooD');
            });

    });

    var $manufDispBtn = $('.manufDispBtn');

    $manufDispBtn.click(function () {
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

        $titleImageImputBut.click(function () {
            var $this = $(this);
            var $titleImageImputEdid = $('.titleImageImputEdid');//Edit form element
            var $titleImagePic = $('.titleImagePic'); //Pictures
            $titleImageImputEdid.toggle();
            $titleImagePic.toggle();
            console.log($this.text());
            if ($this.text() == 'Изменить') {
                $this.text('Отмена');
            }else{
                $this.text('Изменить');
            }
    });




});



