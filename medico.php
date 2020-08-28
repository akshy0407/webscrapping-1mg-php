<?php
include ('vendor/autoload.php');

$path = "https://api.telegram.org/bot1199116258:AAHVP3hAxsTo9AC7nNcIeYFag9Nov-7aZuk";

$update = json_decode(file_get_contents("php://input") , true);

if (isset($update['callback_query']))
{
    

    // Reply with callback_query data
    $message = $update['callback_query']['data'];
    $chatId = $update['callback_query']['from']['id'];

    //call the crawler
    $client = new \Goutte\Client();
    $crawler = $client->request('GET', 'https://www.1mg.com' . $message);
    $fullPageHtml = $crawler->html();
    
    
    $explode = explode("/",$message);
  
  if($explode['1'] == "drugs")
  // else
    {
    //quick tip
    $quick="<b>Quick Tip : </b>";
    for($i=0;$i < 15;$i++){
    try
    {
     
     $quick=$quick."\n".($i+1)." : ". $crawler->filter('#expert_advice_0')
        ->filter(".container-fluid-padded")
        ->filter('li')->eq($i)
        ->text();

    }
    catch(Exception $e)
    {
        break;
    }
        
    }
  
    //prescribed for
      $pre="<b>Prescribed for : </b>";
    for($i=0;$i < 15;$i++){
    try
    {
     
     $pre=$pre."\n".($i+1)." : ". $crawler->filter('.container-fluid-padded')
        ->filter(".marginTop-8")
        ->filter('li')->eq($i)
        ->text();

    }
    catch(Exception $e)
    {
        break;
    }
        
    }
    
    $side="<b>Side Effects : </b>";
    //side effects save in variable
    for($i=0;$i < 15;$i++){
    try
    {
     
     $side=$side."\n".($i+1)." : ".$crawler->filter('#side_effects_0')->filter('li')->eq($i)->text();

    }
    catch(Exception $e)
    {
        break;
    }
        
    }
  

    $details = "Name : " . $crawler->filter(".container-fluid-padded")
        ->filter('h1')
        ->eq(0)
        ->text() . "\nManufacturer : " . $crawler->filter(".FactBox__rowContent__2YA1r")
        ->filter('a')
        ->eq(1)
        ->text() . "\n" . $crawler->filter(".l3Regular")
        ->eq(0)
        ->text() . "\n\n" .$pre. "\n\n" .$side."\n\n".$quick.
        "\n\n<b>Link : </b>https://www.1mg.com".$message."\n\nNote : All the information is been fetched for 1mg and is provided from information purpose only";

    $info = urlencode($details);
    file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info."&parse_mode=html");
    }
    else
    {
     
     /*desc
         $desc=$desc."\n".($i+1)." : ". $crawler->filter('.pNormal')
         ->text();
    //title
         $title=$title."\n".($i+1)." : ". $crawler->filter('.pNormal')
         ->filter('strong')
         ->text();
  //  $details="<b>Product Name : </b>".$title."\n\n<b>Description : </b>".$desc."\n\n<b>Link : </b>https://www.1mg//.com".$message;
  */
  $details="Sorry, No Information is available for this Product . \nSorry for the inconvenience \nWhat i suggest click the link : https://www.1mg.com/".$message;

    $info = urlencode($details);
    file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info."&parse_mode=html");
        
        
    }
    
}

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

$data = ucwords($message);

if ($data == "Hii" || $data == "Hello" || $data == "Hii" || $data == "Intro" || $data == "/start" || $data == "Hey")
{
    intro($chatId, $path);

}
elseif($message == "About me" || $message == "about me" || $message == "About medico" || $message == "About medico" || $message == "about medico" || $message == "about medico")
{
       $info = "Myself Medico, a Information bot. I was born or you can say developed on 28th August,2020. Nothing else to say :-)  \n https://t.me/Medicoonlinebot";
    $info = urlencode($info);

    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$info);

}

else
{
    medsearch($path, $chatId, $data);

}
//intro
function intro($chatId, $path)
{

    $info = "Hey, I am Medico a telegram bot \nHow can i help you ?\nSend me any medicine names and i will send details for the same.\nMedico Bot gets all of its information from 1mg.com \nDisclaimer: \nMedico sole intention is to ensure that its user get information that is expert-reviewed, accurate and trustworthy. However, the information contained herein should NOT be used as a substitute for the advice of a qualified physician. The information provided here is for informational purposes only. This may not cover all possible side effects, drug interactions or warnings or alerts. Please consult your doctor and discuss all your queries related to any disease or medicine. We intend to support, not replace, the doctor-patient relationship.";
    $info = urlencode($info);

    file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info);

}

//search  function
function medsearch($path, $chatId, $data)
{
    $details = "Hey you searched for : " . $data . "\n";
    $keyarray = array();
    $data = str_replace(' ', '%20', $data);
    $client = new \Goutte\Client();
    $crawler = $client->request('GET', 'https://www.1mg.com/search/all?name=' . $data);
    $fullPageHtml = $crawler->html();
    $count = 0;
    try
    {

        $count = substr($crawler->filter('.style__countInfo___sVIyR')
            ->text() , 0, 2);
    }
    catch(Exception $e)
    {

    }

    if ($count > 0)
    {

        $count = substr($crawler->filter('.style__countInfo___sVIyR')
            ->text() , 0, 2);
        try
        {
            if ($crawler->filter('.style__horizontal-card___1Zwmt') != "")
            {
                for ($i = 0;$i < $count;$i++)
                {
                    $details = "Name :" . $crawler->filter('.style__horizontal-card___1Zwmt')
                        ->filter('.style__pro-title___3zxNC')
                        ->eq($i)->text() . "\nPacking : " . $crawler->filter('.style__horizontal-card___1Zwmt')
                        ->filter('.style__pack-size___254Cd')
                        ->eq($i)->text() . "\n" . $crawler->filter('.style__horizontal-card___1Zwmt')
                        ->filter('.style__price-tag___B2csA')
                        ->eq($i)->text() . "\nlink: https://www.1mg.com" . $crawler->filter('.style__horizontal-card___1Zwmt')
                        ->filter('a')
                        ->eq($i)->attr('href') . "\n--------------\n";

                    $keyboard = array(
                        "inline_keyboard" => array(
                            array(
                                array(
                                    "text" => $crawler->filter('.style__horizontal-card___1Zwmt')
                                        ->filter('.style__pro-title___3zxNC')
                                        ->eq($i)->text() ,
                                    "callback_data" => $crawler->filter('.style__horizontal-card___1Zwmt')
                                        ->filter('a')
                                        ->eq($i)->attr('href')
                                )
                            )
                        )
                    );

                    $key = json_encode($keyboard);

                    $info = urlencode($details);

                    file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info . "&reply_markup=" . $key);

                }

            }
        }
        catch(Exception $e)
        {
            try
            {
                for ($i = 0;$i < $count;$i++)
                {
                    $details = "Name :" . $crawler->filter('.style__container___jkjS2')
                        ->filter('.style__pro-title___3G3rr')
                        ->eq($i)->text() . "\nPacking : " . $crawler->filter('.style__container___jkjS2')
                        ->filter('.style__pack-size___3jScl')
                        ->eq($i)->text() . "\nMrp : " . $crawler->filter('.style__container___jkjS2')
                        ->filter('.style__price-tag___KzOkY')
                        ->eq($i)->text() . "\nlink: https://www.1mg.com" . $crawler->filter('.style__container___jkjS2')
                        ->filter('a')
                        ->eq($i)->attr('href') . "\n--------------\n";

                    $keyboard = array(
                        "inline_keyboard" => array(
                            array(
                                array(
                                    "text" => $crawler->filter('.style__container___jkjS2')
                                        ->filter('.style__pro-title___3G3rr')
                                        ->eq($i)->text() ,
                                    "callback_data" => $crawler->filter('.style__container___jkjS2')
                                        ->filter('a')
                                        ->eq($i)->attr('href')
                                )
                            )
                        )
                    );

                    $key = json_encode($keyboard);

                    $info = urlencode($details);

                    file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info . "&reply_markup=" . $key);
                }
            }
            catch(Exception $e)
            {
            }

        }

    }
    else
    {

        $info = "Hey i am really sorry but i failed to find any matching medicine :-(\nDon't worry you can try again\n\n“To keep the body in good health is a duty…otherwise we shall not be able to keep the mind strong and clear.” – Buddha";
        $info = urlencode($info);
        file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . $info);
    }

}

function callRes($message)
{

}
?>
