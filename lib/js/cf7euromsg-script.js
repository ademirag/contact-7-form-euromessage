
function cf7euromsg(){}

cf7euromsg.prototype.onSaveEuroMessageInfo = function(e){
  e.preventDefault();
  var data = {
    action:'cf7euromsg_saveEuroMessageInfo',
    data:{
      username:jQuery("#cf7euromsg_username").val(),
      password:jQuery("#cf7euromsg_password").val()
    }
  }

  jQuery.post(ajaxurl, data, function(response) {
      response = JSON.parse(response);
			if(response.result == 1){
        jQuery("#cf7euromsg_saveEuroMessageInfo").notify(response.message,"success");
      }else{
        jQuery("#cf7euromsg_saveEuroMessageInfo").notify(response.message,"error");
      }
	});
}

cf7euromsg.prototype.onTestEuroMessageInfo = function(e){
  e.preventDefault();
  var data = {
    action:'cf7euromsg_testEuroMessageInfo',
    data:{
      username:jQuery("#cf7euromsg_username").val(),
      password:jQuery("#cf7euromsg_password").val()
    }
  }
  jQuery.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if(response.result == "1"){
      jQuery("#cf7euromsg_testEuroMessageInfo").notify(response.message,"success");
    }else{
      jQuery("#cf7euromsg_testEuroMessageInfo").notify(response.message,"error");
    }

	});
}

cf7euromsg.prototype.onSaveSettings = function(e){
  e.preventDefault();
  var forms = [];
  jQuery(".cfForm").each(function(){
    var form = {id:jQuery(this).attr("data-id"),demography:[]};
    var addToList = jQuery(".addToList",jQuery(this));
    if(addToList.length > 0){
      form.list = addToList.val();
      form.listDataElement = addToList.attr("data-id");
    }
    var demographies = jQuery(".demography",jQuery(this));
    demographies.each(function(){
      if(jQuery(this).val() != ""){
        form.demography.push({key:jQuery(this).val(),value:jQuery(this).attr("data-id")});
      }
    })
    forms.push(form);
  });

  var data = {
    action:'cf7euromsg_saveSettings',
    data:{
      forms:forms
    }
  }
  jQuery.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if(response.result == "1"){
      jQuery("#cf7euromsg_saveSettings").notify(response.message,"success");
    }else{
      jQuery("#cf7euromsg_saveSettings").notify(response.message,"error");
    }

  });

}

var _cf7euromsg = new cf7euromsg();


jQuery(document).ready(function(){
  jQuery(document).on("click","#cf7euromsg_saveEuroMessageInfo",_cf7euromsg.onSaveEuroMessageInfo);
  jQuery(document).on("click","#cf7euromsg_testEuroMessageInfo",_cf7euromsg.onTestEuroMessageInfo);
  jQuery(document).on("click","#cf7euromsg_saveSettings",_cf7euromsg.onSaveSettings);
});
