<?php

namespace VinilShopBundle\Service;



class CapthaService
{

    private $securityNumber;
    private $imgX = 150;  // image width
    private $imgY = 30;   // image height
    private $fontMinSize = 16;  // Min font size
    private $fontMaxSize = 23;   //Max	 font size
    private $maxHorizAngle = 20;   //Максимальный угол отклонения от горизонтали по часовой стрелке и против, по умолчанию-20
    private $textColor;
    private $imageColor;
    private $font;
    private $image;
    public function __construct($securityNumber,$font)
    {
        $this->securityNumber = $securityNumber;
        $this->font = $font;

        $this->image = imagecreate($this->imgX, $this->imgY);
        // colors allocations - text, noise, bg
        $this->textColor = imagecolorallocate($this->image, 0, 0, 0);
        $this->imageColor = imagecolorallocate($this->image, 255, 255, 255);
        // fill the bg of image
        imagefill($this->image, 0, 0, $this->imageColor);
        $this->drawNumber();
    }
    private function drawNumber()
    {
        //  В переменной $number будет храниться число, показанное на изображении
        $num_n = strlen($this->securityNumber);
        for ($n = 0; $n < $num_n; $n++) {
            $num = substr($this->securityNumber, $n, 1);
            $font_size = rand($this->fontMinSize, $this->fontMaxSize);//$this->imgY/2
            $angle = rand(360 - $this->maxHorizAngle, 360 + $this->maxHorizAngle);
            //  вычисление координат для каждой цифры, формулы обеспечивают нормальное расположние
            //  при любых значениях размеров цифры и изображения
            $y = rand(($this->imgY - $font_size) / 4 + $font_size, ($this->imgY - $font_size) / 2 + $font_size);
            $x = rand(($this->imgX / $num_n - $font_size) / 2, $this->imgX / $num_n - $font_size) + $n * $this->imgX / $num_n;
            $this->textColor = imagecolorallocate($this->image, rand(0, 100), rand(0, 100), rand(0, 100));       //цвет текста
            imagettftext($this->image, $font_size, $angle, $x, $y, $this->textColor, $this->font, $num);
        }
    }
    public function output()
    {
        header("Content-type: image/png");
        imagepng($this->image);
    }


}