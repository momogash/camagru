<?php
require_once './core/init.php';

$user = new User();
// var_dump($_POST);

if(!$user->isLoggedIn())
{
    Redirect::to('index.php');
}
else{

if(Input::exists())
{
   if( $img = $_POST['hidden_id']){
   // var_dump($_POST);
    //die();
    $imgName = uniqid('', true).".png"; //gives a name of time format in current micro seconds
    $fileDestination = './uploads/'.$imgName;//$imgPath = "../uploads/gallery/".$baseImgName; //path to server folder
	$imgUrl = str_replace("data:image/png;base64,", "", $img);
	$imgUrl = str_replace(" ", "+", $imgUrl);
    $imgDecoded = base64_decode($imgUrl); //decoded image ready to be used
    file_put_contents($fileDestination, $imgDecoded); //moved to upload folder to be inserted in database

    /* stickers should be added to the decoded webcam image and sent to the file path on server 
    before being uploaded to path
    also before being inserted into DB*/
    DB::getInstance()->insert('images', array(
            'image_name' => $imgName,
            'user_id' =>  Session::get('user'), //$_SESSION['user'],
            'image' => $fileDestination
    ));
}
// if($_POST['hidden_id'] && $_POST['addsticker'])
// {
//     $
//     function addsticker($sticker, $imgDecoded, $) //also try use $imgName
// }
    //var_dump($img);
    //placing the stickers on the images

}
}
?>