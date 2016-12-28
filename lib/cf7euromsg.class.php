<?php

class CF7EuroMsg{

  private $ticket;

  public static $serviceUrl="https://ws.euromsg.com/live/";

  function __construct(){

    add_action("wpcf7_before_send_mail", array($this,"do_the_job"));
    add_action( 'admin_menu', array($this,"adminSubmenu"));
    add_action( 'admin_enqueue_scripts', array( $this, 'adminAssets' ) );
    add_action( 'wp_ajax_cf7euromsg_saveEuroMessageInfo', array($this,'saveEuroMessageInfo' ));
    //add_action( 'wp_ajax_cf7euromsg_testEuroMessageInfo', array($this,'testEuroMessageInfo' ));
    add_action( 'wp_ajax_cf7euromsg_saveSettings', array($this,'saveSettings' ));

    add_filter( "plugin_action_links_".PLUGIN_BASENAME, array($this, 'addSettingsLink' ));

  }

  function log($info){
    $h = fopen(dirname(__FILE__)."/log.txt","w");
    fwrite($h,$info);
    fclose($h);
  }

  function do_the_job ($cf7) {

    $args = array(
      'posts_per_page'   => -1,
      'offset'           => 0,
      'category'         => '',
      'category_name'    => '',
      'orderby'          => 'date',
      'order'            => 'DESC',
      'include'          => '',
      'exclude'          => '',
      'meta_key'         => '',
      'meta_value'       => '',
      'post_type'        => 'wpcf7_contact_form',
      'post_mime_type'   => '',
      'post_paren t'      => '',
      'author'     => '',
      'author_name'    => '',
      'post_status'      => 'publish',
      'suppress_filters' => true
    );
    $forms = get_posts( $args );

    $username = get_option("cf7euromsg_username");
    if($username === false) return;
    $password = get_option("cf7euromsg_password");
    if($password === false) return;

    $ticket = $this->login($username,$password);

    if($ticket == NULL) return;

    foreach($forms as $form){

      if($form->post_title == $cf7->title()){

          if(($settings = get_option("cf7euromsg_settings")) !== false){
            foreach($settings as $formSettings){
              if($formSettings["active"] == "true" && $formSettings["list"] != "0"){
                if($formSettings["id"] == $form->ID){
                  $submission = WPCF7_Submission::get_instance();

                  if ($submission) {

                      $data = $submission->get_posted_data();


                      $lists = $this->querySendLists($ticket);

                      foreach($lists as $list){
                        if($list->ListId == $formSettings["list"]){
                          $email = $data[$formSettings["listDataElement"]];
                          $this->insertMember($ticket,$email);
                          $this->addToSendLists($ticket,$list->ListName,$list->GroupName,$email);
                          break;
                        }
                      }
                  }
                  if(isset($formSettings["demography"])){
                    $demographyValue = array();
                    $submission = WPCF7_Submission::get_instance();

                    if ($submission) {
                      $data = $submission->get_posted_data();
                      $email = $data[$formSettings["listDataElement"]];
                      foreach($formSettings["demography"] as $demography){
                        $d = array("Key"=>$demography["key"],"Value"=>$data[$demography["value"]]);
                        $demographyValue[] = $d;
                      }
                      $this->insertMemberDemography($ticket,$email,$demographyValue);
                    }

                  }
                  break;
                }
              }

            }
          }
          break;
      }
    }
  }

  function adminSubmenu(){
    add_submenu_page( 'wpcf7','Contact Form to euro.message','Contact Form to euro.message', 'manage_options', 'cf7-euromsg', array($this,'adminPage') );
  }

  function adminPage(){
    $ch = curl_init("https://ifconfig.co/");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $rv = curl_exec($ch);

    preg_match('/<code class="ip">([0-9.]+)<\/code>/',$rv,$m);

    curl_close($ch);

    if(isset($m[1]))
      $vars["euromsg_ip"] = $m[1];
    else
      $vars["euromsg_ip"] = __( 'Belirlenemedi',"cf7euromsg" );

    if(($username = get_option("cf7euromsg_username")) !== false){
      $vars["username"] = $username;
      $password = get_option("cf7euromsg_password");
      $ticket = $this->login($username,$password);
      $vars["lists"] = $this->querySendLists($ticket);
    }else{
      $vars["username"] = "";
      $vars["lists"] = array();
    }

    if(($settings = get_option("cf7euromsg_settings")) !== false){
      $vars["settings"] = $settings;
    }else{
      $vars["settings"] = array();
    }


    include(dirname(__FILE__).'/admin_page.php');
  }

  function addSettingsLink( $links){
    $settings_link = '<a href="admin.php?page=cf7-euromsg">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
  }

  function adminAssets(){
    wp_enqueue_style( 'cf7-euromsg-css', CF7EURMSGURL.'lib/css/cf7euromsg-style.css' );
    wp_enqueue_script( 'cf7-euromsg-js', CF7EURMSGURL.'lib/js/cf7euromsg-script.js', array(), '1.0.3', true );
  }

  function saveEuroMessageInfo(){
    $input = $_POST["data"];
    $username = $input["username"];
    $password = $input["password"];

    $this->login($username,$password);
    if($this->ticket == NULL){
      $rv = array("result"=>"0","message"=>__("Euro Message Giriş Bilgileri Testi Başarısız!","cf7euromsg"));
    }else{
      if(get_option("cf7euromsg_username") === false){
        add_option("cf7euromsg_username",$username);
      }else{
        update_option("cf7euromsg_username",$username);
      }

      if(get_option("cf7euromsg_password") === false){
        add_option("cf7euromsg_password",$password);
      }else{
        update_option("cf7euromsg_password",$password);
      }

      $rv = array("result"=>"1","message"=>__("Euro Message Giriş Bilgileri Kaydedildi!","cf7euromsg"));

    }

    echo json_encode($rv);

    wp_die();
  }

/*  function testEuroMessageInfo(){
    $input = $_POST["data"];
    $username = $input["username"];
    $password = $input["password"];
    $this->login($username,$password);
    if($this->ticket == NULL){
      $rv = array("result"=>"0","message"=>__("Euro Message Giriş Bilgileri Testi Başarısız!","cf7euromsg"));
    }else{
      $rv = array("result"=>"1","message"=>__("Euro Message Giriş Bilgileri Testi Başarılı.","cf7euromsg"));
    }
    echo json_encode($rv);
    wp_die();
  }*/

  function saveSettings(){
    $input = $_POST["data"];

    if(get_option("cf7euromsg_settings") !== false){
      $forms = get_option("cf7euromsg_settings");
      for($i = 0; $i < count($forms); $i++){
        if($forms[$i]["id"] == $input["id"]){
          unset($forms[$i]);
          break;
        }
      }
      $forms[] = $input;
      $forms = $this->resolveKeys($forms);
      update_option("cf7euromsg_settings",$forms);
    }else{
      $forms = array($input);
      add_option("cf7euromsg_settings",$forms);
    }
    $rv = array("result"=>"1","message"=>__("Ayarlar kaydedildi!","cf7euromsg"));
    echo json_encode($rv);
//    delete_option("cf7euromsg_settings");
    wp_die();
  }

  function resolveKeys($arr){
    $rv = array();
    foreach($arr as $key=>$value){
      $rv[] = $value;
    }
    return $rv;
  }

  function login($username,$password){
    $wsLogin = new SoapClient(self::$serviceUrl . "auth.asmx?wsdl");
    $r = $wsLogin->Login((object)array("Username"=>$username,"Password"=>$password));

    if(isset($r->LoginResult->ServiceTicket)){
      $this->ticket = $r->LoginResult->ServiceTicket;
    }else{

      unset ($wsLogin);
      return NULL;
    }
    unset ($wsLogin);
    return $this->ticket;
  }

  function querySendLists($serviceTicket){
    $ws = new SoapClient(self::$serviceUrl . "sendlist.asmx?wsdl");
    $rv = $ws->QuerySendLists((object)array("ServiceTicket"=>$serviceTicket))->QuerySendListsResult;
    if(isset($rv->SendLists->EmSendListResponse)){
      $rv = $rv->SendLists->EmSendListResponse;
    }else{
      $rv = NULL;
    }
    unset ($ws);
    return $rv;
  }

  function insertMember($serviceTicket, $email){
   $wsPost = new SoapClient(self::$serviceUrl . "member.asmx?wsdl");
   $MemberSend = array(
         "ServiceTicket" => $serviceTicket,
         "Key"=>"EMAIL",
         "Value"=>$email,
         "ForceUpdate"=>"true",
         "DemograficData"=>
         array
         (
             array("Key"=>"STATUS","Value"=>"A"),
             array("Key"=>"EMAIL_PERMIT","Value"=>"Y")
         )
     );

   $rPost = $wsPost->InsertMemberDemography($MemberSend)->InsertMemberDemographyResult ;
  // print_r($rPost);
 }

 function insertMemberDemography($serviceTicket,$email,$demography){
   $keys = array();
   $d = array();
   foreach($demography as $demographyItem){
     if(array_search($demographyItem["Key"],$keys) === false){
       $keys[] = $demographyItem["Key"];
       $d[] = array("Key"=>$demographyItem["Key"],"Value"=>$demographyItem["Value"]);
     }
   }
  $wsPost = new SoapClient(self::$serviceUrl . "member.asmx?wsdl");
  $MemberSend = array(
        "ServiceTicket" => $serviceTicket,
        "Key"=>"EMAIL",
        "Value"=>$email,
        "ForceUpdate"=>"true",
        "DemograficData"=>$d
    );

  $rPost = $wsPost->InsertMemberDemography($MemberSend)->InsertMemberDemographyResult ;
  //print_r($rPost);
}

    function addToSendLists($serviceTicket,$listeadi,$grupadi,$email){
     $wsPost = new SoapClient(self::$serviceUrl . "member.asmx?wsdl");
     $MemberSend = array(
           "ServiceTicket" => $serviceTicket,
           "Key"=>"EMAIL",
           "Value"=>"$email",
           "Move"=>TRUE,
           "SendLists"=>array(
             "EmSendList"=>array(
               "ListName"=>"$listeadi",
               "GroupName"=>"$grupadi"
             )
           )
       );
     $rPost = $wsPost->AddToSendLists($MemberSend)->AddToSendListsResult ;
     //print_r($rPost);
     unset($wsPost,$MemberSend,$rPost);
   }

  static function getFieldInfo($fieldStr){
    //echo $fieldStr."<br>";
    $fieldStr = preg_replace('/\s+/', ' ',$fieldStr);
    $parts = explode(' ',$fieldStr);
    if(count($parts) < 2) return NULL;
    $parts[0] = str_replace("*","",$parts[0]);
    $rv = array(
      "type"=>$parts[0]
    );
    if($rv["type"] == "submit") return NULL;
    for($i = 2; $i < count($parts); $i++){
      if($parts[$i] == "placeholder"){
        $rv["title"] = $parts[$i+1];
        for($j = $i+2; $j < count($parts); $j++){
          $rv["title"] .= " ".$parts[$j];
        }
        break;
      }
    }

    if(isset($parts[1]))
      $rv["name"] = $parts[1];
    else
      $rv["name"] = $parts[0];
    if(!isset($rv["title"])){
      $rv["title"] = $rv["name"];
    }
    //var_dump($rv);
    return $rv;
  }

}
