<?php 

if(!function_exists("Webp2")):
    function Webp2($source, $quality = 70)
    {
        if (file_exists($source)) :
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            $image = null;
            if ($extension !== "webp") :
                if ($extension == "jpeg" || $extension == "jpg") :
                    $image = @imagecreatefromjpeg($source);
                    if (!$image) :
                        $image = imagecreatefromstring(file_get_contents($source));
                    endif;
                elseif ($extension == "gif") :
                    $image = @imagecreatefromgif($source);
                    if (!$image) :
                        $image = imagecreatefromstring(file_get_contents($source));
                    endif;
                elseif ($extension == "png") :
                    $image = @imagecreatefrompng($source);
                    if (!$image) :
                        $image = imagecreatefromstring(file_get_contents($source));
                    endif;
                else :
                    if ($extension == "jpg" || $extension == "png" || $extension == "gif" || $extension == "wbm" || $extension == "gd2" || $extension == "bmp" || $extension == "webp" || $extension == "jpeg") :
                        $image = imagecreatefromstring(file_get_contents($source));
                    else :
                        $image = null;
                    endif;
                endif;
                $oldSource = $source;
                $source = substr($source, 0, strrpos($source, "."));
                $source .= ".webp";
                if (!empty($image)) :
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    $img =  imagewebp($image, $source, (int)$quality);
                    imagedestroy($image);
                else :
                    $img = null;
                endif;
                if (file_exists($oldSource)) :
                    @unlink($oldSource);
                endif;
                return $img;
            endif;
        endif;
    }
endif;