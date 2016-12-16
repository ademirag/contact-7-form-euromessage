<div class="wrapper">

<h1><?php _e("Contact Form - Euro Message Ayarları","cf7euromsg")?><h1>

<h2>Euro Message'da Tanımlanması Gereken IP:</h2>
<p><?php echo $vars["euromsg_ip"]?></p>

<h2><?php _e("Euro Message Giriş Bilgileri","cf7euromsg")?></h2>
<div class="form">
  <div class="field">
    <label for="cf7euromsg_username"><?php _e("Kullanıcı Adı:","cf7euromsg")?></label>
    <input type="text" id="cf7euromsg_username" value="<?php echo $vars["username"]?>" />
  </div>
  <div class="field">
    <label for="cf7euromsg_password"><?php _e("Şifre:","cf7euromsg")?></label>
    <input type="password" id="cf7euromsg_password" />
  </div>
  <div class="submit">
    <button id="cf7euromsg_saveEuroMessageInfo"><?php _e("Bilgileri Kaydet","cf7euromsg")?></button>
    <button id="cf7euromsg_testEuroMessageInfo"><?php _e("Bilgileri Test Et","cf7euromsg")?></button>
  </div>
</div>

<hr>

<h2><?php _e("Contact Form Form İçerikleri", "cf7euromsg") ?></h2>
<h3><?php _e("Demografik bilgi ekleme kısımlarına, ilgili bilginin anahtarını girmelisiniz. Euro Message, anahtar listesini sunmadığı için, Euro Message yönetim sayfasından Ayarlar > Demografik Bilgiler kısmından ilgili veriyi sizin almanız gerekmektedir.", "cf7euromsg") ?></h3>
<?php $args = array(
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
	'post_parent'      => '',
	'author'	   => '',
	'author_name'	   => '',
	'post_status'      => 'publish',
	'suppress_filters' => true
);
$forms = get_posts( $args );


foreach($forms as $form):

$formData = array();

for($i = 0; $i < count($vars["settings"]); $i++){

  if ($vars["settings"][$i]["id"] == $form->ID) {
    $formData = $vars["settings"][$i];
    break;
  }

}
?>
<div class="item">
<h1><?php echo $form->post_title?></h1>
<table data-id="<?php echo $form->ID?>" class="cfForm">
  <thead>
    <th><?php _e("Alan Başlığı","cf7euromsg")?></th>
    <th><?php _e("Alan Türü","cf7euromsg")?></th>
    <th><?php _e("Listeye Ekle","cf7euromsg")?></th>
    <th><?php _e("Demografik Bilgi Ekle","cf7euromsg")?></th>
  </thead>
  <tbody>
<?php
preg_match_all('/\[([^\]]+)\]/',$form->post_content,$fields);
if(isset($fields[1])){
  foreach($fields[1] as $field){

    $fieldInfo = CF7EuroMsg::getFieldInfo($field);
    if($fieldInfo == NULL) continue;
    $fieldInfo["title"] = str_replace("\"","'",$fieldInfo["title"]);
    echo "<tr>";

    echo "<td>".$fieldInfo["title"]."</td>";

    echo "<td>".$fieldInfo["type"]."</td>";

    if($fieldInfo["type"] == "email"){
      $emailValue = "0";
      if($formData["listDataElement"] == $fieldInfo["name"]){
        $emailValue = $formData["list"];
      }
      ?><td>
        <select class="addToList" data-id="<?php echo $fieldInfo["name"]?>">
          <option value="0" <?php if($emailValue == "0") echo "selected=\"selected\""?>><?php _e("(Listeye ekleme)","cf7euromsg");?></option>
          <?php foreach($vars["lists"] as $listItem): ?>
            <option <?php if($emailValue == $listItem->ListId) echo "selected=\"selected\""?> value="<?php echo $listItem->ListId?>"><?php echo $listItem->GroupName?> &gt; <?php echo $listItem->ListName?></option>
          <?php endforeach; ?>
        </select>
      </td><?php
    }else{
      echo "<td></td>";
    }

    if($fieldInfo["type"] != "email" && $fieldInfo["type"] != "checkbox"){
      $value = "";
      if(isset($formData["demography"])){
        for($i = 0; $i < count($formData["demography"]); $i++){
          if($formData["demography"][$i]["value"] == $fieldInfo["name"]){
            $value = $formData["demography"][$i]["key"];
          }
        }
      }
      ?><td>
        <input type="text" class="demography" value="<?php echo $value?>" data-id="<?php echo $fieldInfo["name"]?>">
      </td><?php
    }else{
      echo "<td></td>";
    }

    echo "<td></td>";

    echo "</tr>";
  }
}
?>
    </tbody>
  </table>

</div>
<?php

endforeach;
?>
<div class="controls">
  <button id="cf7euromsg_saveSettings"><?php _e("Ayarları Kaydet","cf7euromsg")?></button>
</div>
<hr>
<?php
//var_dump($forms);
?>

</div>
