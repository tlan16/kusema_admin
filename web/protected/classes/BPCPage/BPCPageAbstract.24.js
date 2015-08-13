var BPCPageJs=new Class.create;
BPCPageJs.prototype={modalId:"page_modal_box_id",_htmlIDs:{},_ajaxRequest:null,callbackIds:{},initialize:function(){},setCallbackId:function(a,b){this.callbackIds[a]=b;return this},getCallbackId:function(a){if(void 0===this.callbackIds[a]||null===this.callbackIds[a])throw"Callback ID is not set for:"+a;return this.callbackIds[a]},setHTMLID:function(a,b){this._htmlIDs[a]=b;return this},getHTMLID:function(a){return this._htmlIDs[a]},getFormGroup:function(a,b,c){var d;c=c||!1;d=new Element("div",{"class":"form-group"});
a&&d.insert({bottom:a.addClassName("control-label")});b&&(!0===c&&b.addClassName("form-control"),d.insert({bottom:b}));return d},postAjax:function(a,b,c,d){this._ajaxRequest=new Prado.CallbackRequest(a,c);this._ajaxRequest.setCallbackParameter(b);a=d||3E4;3E4>a&&(a=3E4);this._ajaxRequest.setRequestTimeOut(a);this._ajaxRequest.dispatch();return this._ajaxRequest},abortAjax:function(){null!==tmp.me._ajaxRequest&&tmp.me._ajaxRequest.abort()},getResp:function(a,b,c){if(!0===(!0!==b?!1:!0))return a;if(a&&
a.isJSON()){a=a.evalJSON();if(0!==a.errors.size()){b="Error: \n\n"+a.errors.join("\n");if(!0===c)throw b;return alert(b)}return a.resultData}},getCurrency:function(a,b,c,d,f){var g,e,h;g=isNaN(c=Math.abs(c))?2:c;b=void 0==b?"$":b;d=void 0==d?".":d;f=void 0==f?",":f;c=0>a?"-":"";e=parseInt(a=Math.abs(+a||0).toFixed(g))+"";h=3<(h=e.length)?h%3:0;return b+c+(h?e.substr(0,h)+f:"")+e.substr(h).replace(/(\d{3})(?=\d)/g,"$1"+f)+(g?d+Math.abs(a-e).toFixed(g).slice(2):"")},getValueFromCurrency:function(a){return a?
(a+"").replace(/\s*/g,"").replace(/\$/g,"").replace(/,/g,""):""},keydown:function(a,b,c,d){d=d?d:13;if(!(a.which&&a.which==d||a.keyCode&&a.keyCode==d))return"function"===typeof c&&c(),!0;"function"===typeof b&&b();return!1},getAlertBox:function(a,b){return(new Element("div",{"class":"alert alert-dismissible",role:"alert"})).insert({bottom:(new Element("button",{"class":"close","data-dismiss":"alert"})).insert({bottom:(new Element("span",{"aria-hidden":"true"})).update("&times;")}).insert({bottom:(new Element("span",
{"class":"sr-only"})).update("Close")})}).insert({bottom:(new Element("strong")).update(a)}).insert({bottom:b})},_signRandID:function(a){a.id||(a.id="input_"+String.fromCharCode(65+Math.floor(26*Math.random()))+Date.now());return this},_markFormGroupError:function(a,b){var c={me:this};a.up(".form-group")&&(a.store("clearErrFunc",function(b){a.up(".form-group").removeClassName("has-error");jQuery("#"+a.id).tooltip("hide").tooltip("destroy").show()}).up(".form-group").addClassName("has-error"),c.me._signRandID(a),
jQuery("#"+a.id).tooltip({trigger:"manual",placement:"auto",container:"body",placement:"bottom",html:!0,title:b,content:b}).tooltip("show"),$(a).observe("change",function(){c.func=$(a).retrieve("clearErrFunc");"function"===typeof c.func&&c.func()}));return c.me},_collectFormData:function(a,b,c,d){var f,g,e,h,k,m,l;f=this;g={};e=!1;h=!0===d?!0:!1;$(a).getElementsBySelector("["+b+"]").each(function(a){k=c?a.readAttribute(c):null;m=a.readAttribute(b);!0!==h&&a.hasAttribute("required")&&$F(a).blank()&&
(f._markFormGroupError(a,"This is requried"),e=!0);l="checkbox"!==a.readAttribute("type")?$F(a):$(a).checked;if(a.hasAttribute("validate_currency")||a.hasAttribute("validate_number"))!0!==h&&null===f.getValueFromCurrency(l).match(/^(-)?\d+(\.\d{1,4})?$/)&&(f._markFormGroupError(a,a.hasAttribute("validate_currency")?a.readAttribute("validate_currency"):a.hasAttribute("validate_number")),e=!0),f.getValueFromCurrency(l);null!==k&&void 0!==k?(g[k]||(g[k]={}),g[k][m]=l):g[m]=l});return!0===e?null:g},showModalBox:function(a,
b,c,d,f,g){var e;c=!0===c?!0:!1;g=!0===g?!0:!1;d=d||null;$(this.modalId)?(e=jQuery("#"+this.modalId),f=e.find(".modal-dialog").removeClass("modal-sm").removeClass("modal-lg").addClass(!0===c?"modal-sm":"modal-lg"),e.find(".modal-title").html(a),e.find(".modal-body").html(b),0<e.find(".modal-footer").length?null!==d?e.find(".modal-footer").html(d):e.find(".modal-footer").remove():null!==d&&jQuery('<div class="modal-footer"></div>').html(d).appendTo(f.find(".modal-content"))):(a=(new Element("div",
{id:this.modalId,"class":"modal",tabindex:"-1",role:"dialog","aria-hidden":"true","aria-labelledby":"page-modal-box"})).insert({bottom:(new Element("div",{"class":"modal-dialog "+(!0===c?"modal-sm":"modal-lg")})).insert({bottom:(new Element("div",{"class":"modal-content"})).insert({bottom:(new Element("div",{"class":"modal-header"})).insert({bottom:(new Element("div",{"class":"close",type:"button","data-dismiss":"modal"})).insert({bottom:(new Element("span",{"aria-hidden":"true"})).update("&times;")})}).insert({bottom:(new Element("strong",
{"class":"modal-title"})).update(a)})}).insert({bottom:(new Element("div",{"class":"modal-body"})).update(b)}).insert({bottom:null===d?"":(new Element("div",{"class":"modal-footer"})).update(d)})})}),$$("body")[0].insert({bottom:a}),e=jQuery("#"+this.modalId),!0===g&&e.modal({backdrop:"static",keyboard:!1}),f&&"object"===typeof f&&$H(f).each(function(a){e.on(a.key,a.value)}));e.hasClass("in")||e.modal().show();return this},hideModalBox:function(){jQuery("#"+this.modalId).modal("hide");return this},
getLoadingImg:function(){return Element("span",{"class":"loading-img fa fa-refresh fa-5x fa-spin"})},loadUTCTime:function(a){var b;b=a.strip().split(" ");a=b[0].split("-");b=b[1].split(":");return new Date(Date.UTC(a[0],1*a[1]-1,a[2],b[0],b[1],b[2]))},observeClickNDbClick:function(a,b,c){$(a).observe("click",function(d){!0===$(a).retrieve("alreadyclicked")?($(a).store("alreadyclicked",!1),$(a).retrieve("alreadyclickedTimeout")&&clearTimeout($(a).retrieve("alreadyclickedTimeout")),"function"===typeof c&&
c(d)):($(a).store("alreadyclicked",!0),$(a).store("alreadyclickedTimeout",setTimeout(function(){$(a).store("alreadyclicked",!1);"function"===typeof b&&b(d)},300)))});return this},getUrlParam:function(a){a=a.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");a=(new RegExp("[\\?&]"+a+"=([^&#]*)")).exec(location.search);return null===a?"":decodeURIComponent(a[1].replace(/\+/g," "))},openInNewTab:function(a){window.open(a,"_blank").focus();return this},sleep:function(a){var b;b=(new Date).getTime();for(var c=
0;1E7>c&&!((new Date).getTime()-b>a);c++);}};