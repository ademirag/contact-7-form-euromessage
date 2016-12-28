function cf7euromsg_setCounts(active,deactive,total){
  jQuery(".active.count").html("("+active+")");
  jQuery(".active.count").attr("data-count",active);
  jQuery(".deactive.count").html("("+deactive+")");
  jQuery(".deactive.count").attr("data-count",deactive);
  jQuery(".total.count").html("("+total+")");
  jQuery(".total.count").attr("data-count",total);
}

function cf7euromsg(){

  this.forms = new Object();

}

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
        window.location.href = window.location+'&notice='+response.message;
      }else{
        jQuery("#notification").html('<div class="error notice"><p>'+response.message+'</p></div>');
      }
	});
}
/*
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
}*/

/*cf7euromsg.prototype.onSaveSettings = function(e){
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
      //jQuery("#cf7euromsg_saveSettings").notify(response.message,"success");
      alert('ok');
    }else{
      alert('err');
      //jQuery("#cf7euromsg_saveSettings").notify(response.message,"error");
    }

  });

}*/

cf7euromsg.prototype.onFilter = function(filter,a){
  jQuery("#the-list tr").removeClass("hidden");
  if(filter != null){
    jQuery("#the-list tr."+filter).addClass("hidden");
  }
  jQuery(".subsubsub .current").removeClass("current");
  jQuery(a).addClass("current");
}

cf7euromsg.prototype.onFormTitleClick = function(e){
  e.preventDefault();
  var formId = jQuery(this).attr("data-id");
  _cf7euromsg.forms[formId] = jQuery(".form[data-id='"+formId+"']").html();
  jQuery(".form[data-id='"+formId+"']").css("display","block");
}

cf7euromsg.prototype.onFormCancel = function(e){
  e.preventDefault();
  var formId = jQuery(this).attr("data-id");
  jQuery(".form[data-id='"+formId+"']").html(_cf7euromsg.forms[formId]);
  jQuery(".form[data-id='"+formId+"']").css("display","none");
}

cf7euromsg.prototype.onFormSave = function(e){
  e.preventDefault();
  var form = jQuery(".form[data-id='"+jQuery(this).attr("data-id")+"']");
  var formData = {id:jQuery(this).attr("data-id"),demography:[]};
  var activeCheckbox = jQuery("#activateButton-"+jQuery(this).attr("data-id"));
  if(activeCheckbox.is(":checked")){
    formData.active = true;
  }else{
    formData.active = false;
  }
  formData.list = jQuery("select.list",form).val();
  formData.listDataElement = jQuery("#listDataElement-"+formData.id).val();
  var demographies = jQuery(".demography",form);
  demographies.each(function(){
    if(jQuery(this).val() != ""){
      formData.demography.push({key:jQuery(this).val(),value:jQuery(this).attr("data-id")});
    }
  });

  var data = {
    action:'cf7euromsg_saveSettings',
    data:formData
  }
  jQuery.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    jQuery(".notification",form).css("opacity",1);
    if(response.result == "1"){
      jQuery(".notification",form).html('<div class="updated notice"><p>'+response.message+'</p></div>');

      //console.log(_cf7euromsg);
      //_cf7euromsg.forms[formData.id] = form.html();
      /*console.log(_cf7euromsg);
      console.log(formData.id);
      console.log(form);*/

      var activeCount = Number(jQuery(".active.count").attr("data-count"));
      var deactiveCount = Number(jQuery(".deactive.count").attr("data-count"));
      var totalCount = Number(jQuery(".total.count").attr("data-count"));

      if(formData.active){
        if(jQuery(".dashicons-yes[data-id='"+formData.id+"']").hasClass("hidden")){
          jQuery(".dashicons-yes[data-id='"+formData.id+"']").removeClass("hidden");
        }
        if(!jQuery(".dashicons-no-alt[data-id='"+formData.id+"']").hasClass("hidden")){
          jQuery(".dashicons-no-alt[data-id='"+formData.id+"']").addClass("hidden");
        }
        activeCount++;
        deactiveCount--;
        form.parents("tr").each(function(){
          if(jQuery(this).hasClass("deactive")){
            jQuery(this).removeClass("deactive");
            jQuery(this).addClass("active");
          }
        });
      }else{

        if(!jQuery(".dashicons-yes[data-id='"+formData.id+"']").hasClass("hidden")){
          jQuery(".dashicons-yes[data-id='"+formData.id+"']").addClass("hidden");
        }
        if(jQuery(".dashicons-no-alt[data-id='"+formData.id+"']").hasClass("hidden")){
          jQuery(".dashicons-no-alt[data-id='"+formData.id+"']").removeClass("hidden");
        }
        activeCount--;
        deactiveCount++;
        form.parents("tr").each(function(){
          if(jQuery(this).hasClass("active")){
            jQuery(this).removeClass("active");
            jQuery(this).addClass("deactive");
          }
        });
      }
      cf7euromsg_setCounts(activeCount,deactiveCount,totalCount);
      jQuery(".subsubsub .current").click();
    }else{
      jQuery(".notification",form).html('<div class="error notice"><p>'+response.message+'</p></div>');
    }

    setTimeout(function(){
      //jQuery(".notification",form).html("");
      jQuery(".notification",form).animate({opacity:0},{
        complete:function(){
            jQuery(".notification",form).html("");
          }
        }
      );
    },5000);

  });

}

cf7euromsg.prototype.onBulkAction = function(action){
  console.log(action);
  jQuery("#the-list > tr").each(function(){

    var tr = jQuery(this);

    var id = tr.attr("id").split("-")[1];
    if(jQuery("#cb-select-"+id).is(":checked")){
      if(tr.hasClass("active") && action == "deactivate"){
        jQuery("#activateButton-"+id).click();
        tr.removeClass("active");
        tr.addClass("deactive");
        jQuery("button.save",tr).click();
      }else if(tr.hasClass("deactive") && action == "activate"){
        jQuery("#activateButton-"+id).click();
        tr.removeClass("deactive");
        tr.addClass("active");
        jQuery("button.save",tr).click();
      }
    }
    jQuery(".subsubsub .current").click();
  })
}

var _cf7euromsg = new cf7euromsg();


jQuery(document).ready(function(){
  jQuery(document).on("click","#cf7euromsg_saveEuroMessageInfo",_cf7euromsg.onSaveEuroMessageInfo);
  //jQuery(document).on("click","#cf7euromsg_testEuroMessageInfo",_cf7euromsg.onTestEuroMessageInfo);
  //jQuery(document).on("click","#cf7euromsg_saveSettings",_cf7euromsg.onSaveSettings);
  jQuery(document).on("click",".form-title", _cf7euromsg.onFormTitleClick);
  jQuery(document).on("click",".form .cancel", _cf7euromsg.onFormCancel);
  jQuery(document).on("click",".form .save", _cf7euromsg.onFormSave);
  jQuery(document).on("click",".all-button", function(e){
    e.preventDefault();
    _cf7euromsg.onFilter(null,this);
  });
  jQuery(document).on("click",".active-button", function(e){
    e.preventDefault();
    _cf7euromsg.onFilter("deactive",this);
  });
  jQuery(document).on("click",".deactive-button", function(e){
    e.preventDefault();
    _cf7euromsg.onFilter("active",this);
  });
  jQuery(document).on("click","#bulkActionButton", function(e){
    e.preventDefault();
    var action = jQuery("#bulkActionSelector").val();
    if(action != -1){
      _cf7euromsg.onBulkAction(action);
    }
  })
});
