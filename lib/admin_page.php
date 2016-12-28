<script type="text/javascript">

  var cf7euromsgSettings = JSON.parse('<?php echo json_encode($vars["settings"])?>');

</script>

<div class="wrapper">

<h1><?php _e("Contact Form to euro.message","cf7euromsg")?><h1>
<div id="notification">
<?php

if(isset($_GET["notice"])){?>

  <div class="updated notice"><p><?php echo $_GET["notice"]?></p></div>

<?php

}
?>

</div>
<h2><?php _e("euro.message Web Servis","cf7euromsg")?></h2>
<p><?php _e("Lütfen euro.message web servisi kullanıcı adınızı ve şifrenizi kaydediniz. euro.message, web servislerine IP bazlı erişim izni vermektedir. Gerekli izinlerin sağlanması için aşağıdaki formda yazan IP adresinizi de euro.message teknik ekibine iletmeniz gerekmektedir.","cf7euromsg");?></p>

<table class="optiontable form-table">
  <tbody>
      <tr valign="top">
        <th scope="row"><label for="cf7euromsg_username"><?php _e("Kullanıcı Adı","cf7euromsg")?></label></th>
        <td><input type="text" id="cf7euromsg_username" value="<?php echo $vars["username"]?>" class="regular-text"></td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="cf7euromsg_password"><?php _e("Şifre","cf7euromsg")?></label></th>
        <td><input type="password" id="cf7euromsg_password" class="regular-text"></td>
      </tr>
      <tr valign="top">
        <th scope="row"><?php _e("IP Adresiniz","cf7euromsg")?></th>
        <td><input type="text" disabled="disabled" value="<?php echo $vars["euromsg_ip"]?>" class="regular-text"></td>
      </tr>
  </tbody>
</table>
<div class="submit">
  <button class="button-primary" id="cf7euromsg_saveEuroMessageInfo"><?php _e("Bilgileri Kaydet","cf7euromsg")?></button>
</div>

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
?>

<h2><?php _e("Kayıtlı Formlar","cf7euromsg")?></h2>
<p><?php _e("Aşağıda sitenizde kayıtlı bulunan tüm Contact Form 7 formlarını görebilirsiniz. Bu formlardan bilgilerini euro.message ile paylaşmak istediklerinizi Aktive Et'e tıklayarak düzenleyebilirsiniz.","cf7euromsg");?></p>
<ul class="subsubsub">
	<li><a href="#" class="current all-button"><?php _e("All")?> <span class="count total"></span></a> |</li>
	<li><a href="#" class="active-button"><?php _e("Aktif","cf7euromsg")?> <span class="count active"></span></a></li>
  <li><a href="#" class="deactive-button"><?php _e("Deaktif","cf7euromsg")?> <span class="count deactive"></span></a></li>
</ul>
<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text"><?php echo _e("Select bulk action")?></label>
      <select name="action" id="bulkActionSelector">
          <option value="-1"><?php _e("Bulk Actions")?></option>
	        <option value="activate"><?php _e("Activate")?></option>
	        <option value="deactivate"><?php _e("Deactivate")?></option>
        </select>
  <input type="button" id="bulkActionButton" class="button action" value="<?php _e("Apply")?>">
</div>
<table class="wp-list-table widefat fixed striped pages cf7euromsg">
	<thead>
	<tr>
		<td  class="manage-column column-cb check-column">
      <label class="screen-reader-text" for="cb-select-all-1"><?php _e("Select All")?></label>
      <input  type="checkbox">
    </td>
    <th scope="col" class="manage-column column-title column-primary sortable desc">
      <a href="#">
        <span><?php _e("Kayıtlı formlar","cf7euromsg");?></span>
        <span class="sorting-indicator"></span>
      </a>
    </th>
    <th scope="col" class="manage-column column-author"><?php _e("Active")?></th>
    <th scope="col" class="manage-column column-author"><?php _e("Author")?></th>
    <th scope="col" class="manage-column column-date sortable asc">
      <a href="#"><span><?php _e("Date")?></span><span class="sorting-indicator"></span></a>
    </th>
  </tr>
	</thead>

	<tbody id="the-list">
    <?php

    $activeCount = 0;
    $deactiveCount = 0;
    $totalCount = 0;

    foreach($forms as $form):
      setup_postdata($form);

//var_dump($form);
    $formData = array();

    for($i = 0; $i < count($vars["settings"]); $i++){

      if ($vars["settings"][$i]["id"] == $form->ID) {
        $formData = $vars["settings"][$i];
        break;
      }

    }

    $postStatus = __("Published");
    if($form->post_status != "publish"){
      $postStatus = __("Unpublished");
    }

    $active = isset($formData["active"]) ? ($formData["active"] == "true") : false;

    if($active){
      $activeCount++;
    }else{
      $deactiveCount++;
    }
    $totalCount++;

    $postDate = date("Y/m/d",strtotime($form->post_date));

    $author = get_the_author();

    //var_dump($author);

    ?>
			<tr id="post-<?php echo $form->ID?>" class="<?php echo ($active ? 'active' : 'deactive')?> iedit author-self level-0 post-<?php echo $form->ID?> type-page status-publish hentry">
			     <th scope="row" class="check-column">
             <label class="screen-reader-text" for="cb-select-<?php echo $form->ID?>"><?php _e("Select")?></label>
			       <input id="cb-select-<?php echo $form->ID?>" type="checkbox" name="selectedPost[]" value="<?php echo $form->ID?>">
					</th>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
            <strong><a class="row-title form-item form-title" href="#" data-id="<?php echo $form->ID?>" aria-label="“<?php echo $form->post_title?>” (Edit)"><?php echo $form->post_title?></a></strong>
            <div class="form" data-id="<?php echo $form->ID?>">
              <div class="notification"></div>
              <p><?php _e("Bu form bilgileri euro.message veritabanına kaydedilmemektedir. Kaydetmek istiyorsanız lütfen formu aktifleştirin.","cf7euromsg")?></p>
              <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="activateButton-<?php echo $form->ID?>" <?php if($active) echo 'checked'?> >
                <label class="onoffswitch-label" for="activateButton-<?php echo $form->ID?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
              </div>
              <p><?php _e("Bu formdaki bilgilerin gönderildiği zaman hangi euro.message listenize kaydedilmesini istiyorsunuz:","cf7euromsg")?></p>
              <div class="list-container">
                <select class="list">
                  <?php foreach($vars["lists"] as $listItem): ?>
    						        <option value="<?php echo $listItem->ListId?>" <?php if(isset($formData["list"]) && $listItem->ListId == $formData["list"]) echo 'selected="selected" '?>><?php echo $listItem->GroupName?> &gt; <?php echo $listItem->ListName?></option>
                  <?php endforeach;?>
                </select>
    					</div>
              <p><?php _e("Bu formdaki e-posta dışındaki alanları euro.message demografik alanları ile eşleştirebilirsiniz. Bu şekilde kullanıcıların ad, soyad, meslek gibi diğer bilgilerini de euro.message veritabanına kaydedebilirsiniz. Bunun için aşağıdaki alanların yanlarına euro.message panelinizdeki eşleşmesini istediğiniz demografik alanın ismini yazmanız gerekmektedir.","cf7euromsg")?></p>
              <table class="demography-table">
                <thead>
                  <tr>
                    <th><?php _e("Alan Başlığı")?></th>
                    <th><?php _e("Alan Türü")?></th>
                    <th><?php _e("euro.message Demografik Alan Adı")?></th>
                  </tr>
                </thead>
                <tbody>
                <?php

                  $formHTML = WPCF7_ContactForm::get_instance($form)->get_properties()["form"];

                  preg_match_all('/\[([^\]]+)\]/',$formHTML,$fields);

                  if(isset($fields[1])){
                    foreach($fields[1] as $field){

                      $fieldInfo = CF7EuroMsg::getFieldInfo($field);

                      if($fieldInfo == NULL) continue;
                      $fieldInfo["title"] = str_replace("\"","'",$fieldInfo["title"]);
                  ?>

                    <tr>
                      <td><?php echo $fieldInfo["title"]?></td>
                      <td><?php echo $fieldInfo["type"]?></td>

                      <?php

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
                        if($fieldInfo["type"] == "email"){
                          ?><input type="hidden" id="listDataElement-<?php echo $form->ID?>" value="<?php echo $fieldInfo["name"]?>"><?php
                        }
                        echo "<td></td>";
                      }
                      ?>

                    </tr>

                  <?php

                  }

                }

                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th><?php _e("Alan Başlığı")?></th>
                    <th><?php _e("Alan Türü")?></th>
                    <th><?php _e("euro.message<br>Demografik Alan Adı")?></th>
                  </tr>
                </tfoot>
              </table>
              <button type="button" class="button-secondary cancel " data-id="<?php echo $form->ID?>"><?php _e("Cancel")?></button>
              <button type="button" class="button-primary save" data-id="<?php echo $form->ID?>"><?php _e("Update")?></button>
            </div>
	        </td>
          <td class="date column-active" data-colname="Active"><span data-id="<?php echo $form->ID?>" class="dashicons dashicons-yes <?php if(!$active) echo 'hidden';?>"></span><span data-id="<?php echo $form->ID?>" class="dashicons dashicons-no-alt <?php if($active) echo 'hidden';?>"></span></td>
          <td class="date column-author" data-colname="Author"><?php echo $author?></td>
          <td class="date column-date" data-colname="Date"><?php echo $postStatus?><br><abbr title="<?php echo $form->post_date?>"><?php echo $postDate?></abbr></td>
      </tr>

    <?php
  endforeach;
wp_reset_postdata();
     ?>

		</tbody>

	<tfoot>
    <tr>
  		<td id="cb" class="manage-column column-cb check-column">
        <label class="screen-reader-text" for="cb-select-all-1"><?php _e("Select All")?></label>
        <input id="cb-select-all-1" type="checkbox">
      </td>
      <th scope="col" class="manage-column column-title column-primary sortable desc">
        <a href="#">
          <span><?php _e("Kayıtlı formlar","cf7euromsg");?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th scope="col" class="manage-column column-author"><?php _e("Active")?></th>
      <th scope="col" class="manage-column column-author"><?php _e("Author")?></th>
      <th scope="col" class="manage-column column-date sortable asc">
        <a href="#"><span><?php _e("Date")?></span><span class="sorting-indicator"></span></a>
      </th>
    </tr>

	</tfoot>
</table>
<script type="text/javascript">
  jQuery(document).ready(function(){
    cf7euromsg_setCounts(<?php echo $activeCount?>,<?php echo $deactiveCount?>,<?php echo $totalCount?>);
  });
</script>
