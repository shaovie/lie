
/**
* Bootstrap.js by @mdo and @fat, extended by @ArnoldDaniels.
* plugins: bootstrap-fileupload.js
* Copyright 2012 Twitter, Inc.
* http://www.apache.org/licenses/LICENSE-2.0.txt
*/
!function(e){var t=function(t,n){this.$element=e(t),this.type=this.$element.data("uploadtype")||(this.$element.find(".thumbnail").length>0?"image":"file"),this.$input=this.$element.find(":file");if(this.$input.length===0)return;this.name=this.$input.attr("name")||n.name,this.$hidden=this.$element.find('input[type=hidden][name="'+this.name+'"]'),this.$hidden.length===0&&(this.$hidden=e('<input type="hidden" />'),this.$element.prepend(this.$hidden)),this.$preview=this.$element.find(".fileupload-preview");var r=this.$preview.css("height");this.$preview.css("display")!="inline"&&r!="0px"&&r!="none"&&this.$preview.css("line-height",r),this.original={exists:this.$element.hasClass("fileupload-exists"),preview:this.$preview.html(),hiddenVal:this.$hidden.val()},this.$remove=this.$element.find('[data-dismiss="fileupload"]'),this.$element.find('[data-trigger="fileupload"]').on("click.fileupload",e.proxy(this.trigger,this)),this.listen()};t.prototype={listen:function(){this.$input.on("change.fileupload",e.proxy(this.change,this)),e(this.$input[0].form).on("reset.fileupload",e.proxy(this.reset,this)),this.$remove&&this.$remove.on("click.fileupload",e.proxy(this.clear,this))},change:function(e,t){if(t==="clear")return;var n=e.target.files!==undefined?e.target.files[0]:e.target.value?{name:e.target.value.replace(/^.+\\/,"")}:null;if(!n){this.clear();return}this.$hidden.val(""),this.$hidden.attr("name",""),this.$input.attr("name",this.name);if(this.type==="image"&&this.$preview.length>0&&(typeof n.type!="undefined"?n.type.match("image.*"):n.name.match(/\.(gif|png|jpe?g)$/i))&&typeof FileReader!="undefined"){var r=new FileReader,i=this.$preview,s=this.$element;r.onload=function(e){i.html('<img src="'+e.target.result+'" '+(i.css("max-height")!="none"?'style="max-height: '+i.css("max-height")+';"':"")+" />"),s.addClass("fileupload-exists").removeClass("fileupload-new")},r.readAsDataURL(n)}else this.$preview.text(n.name),this.$element.addClass("fileupload-exists").removeClass("fileupload-new")},clear:function(e){this.$hidden.val(""),this.$hidden.attr("name",this.name),this.$input.attr("name","");if(navigator.userAgent.match(/msie/i)){var t=this.$input.clone(!0);this.$input.after(t),this.$input.remove(),this.$input=t}else this.$input.val("");this.$preview.html(""),this.$element.addClass("fileupload-new").removeClass("fileupload-exists"),e&&(this.$input.trigger("change",["clear"]),e.preventDefault())},reset:function(e){this.clear(),this.$hidden.val(this.original.hiddenVal),this.$preview.html(this.original.preview),this.original.exists?this.$element.addClass("fileupload-exists").removeClass("fileupload-new"):this.$element.addClass("fileupload-new").removeClass("fileupload-exists")},trigger:function(e){this.$input.trigger("click"),e.preventDefault()}},e.fn.fileupload=function(n){return this.each(function(){var r=e(this),i=r.data("fileupload");i||r.data("fileupload",i=new t(this,n)),typeof n=="string"&&i[n]()})},e.fn.fileupload.Constructor=t,e(document).on("click.fileupload.data-api",'[data-provides="fileupload"]',function(t){var n=e(this);if(n.data("fileupload"))return;n.fileupload(n.data());var r=e(t.target).closest('[data-dismiss="fileupload"],[data-trigger="fileupload"]');r.length>0&&(r.trigger("click.fileupload"),t.preventDefault())})}(window.jQuery)

/*! ============================================================
 * bootstrapSwitch v1.8 by Larentis Mattia @SpiritualGuru
 * http://www.larentis.eu/
 * 
 * Enhanced for radiobuttons by Stein, Peter @BdMdesigN
 * http://www.bdmdesign.org/
 *
 * Project site:
 * http://www.larentis.eu/switch/
 * ============================================================
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 * ============================================================ */
!function($){"use strict";$.fn['bootstrapSwitch']=function(method){var inputSelector='input[type!="hidden"]';var methods={init:function(){return this.each(function(){var $element=$(this),$div,$switchLeft,$switchRight,$label,$form=$element.closest('form'),myClasses="",classes=$element.attr('class'),color,moving,onLabel="ON",offLabel="OFF",icon=false,textLabel=false;$.each(['switch-mini','switch-small','switch-large'],function(i,el){if(classes.indexOf(el)>=0)myClasses=el});$element.addClass('has-switch');if($element.data('on')!==undefined)color="switch-"+$element.data('on');if($element.data('on-label')!==undefined)onLabel=$element.data('on-label');if($element.data('off-label')!==undefined)offLabel=$element.data('off-label');if($element.data('label-icon')!==undefined)icon=$element.data('label-icon');if($element.data('text-label')!==undefined)textLabel=$element.data('text-label');$switchLeft=$('<span>').addClass("switch-left").addClass(myClasses).addClass(color).html(onLabel);color='';if($element.data('off')!==undefined)color="switch-"+$element.data('off');$switchRight=$('<span>').addClass("switch-right").addClass(myClasses).addClass(color).html(offLabel);$label=$('<label>').html("&nbsp;").addClass(myClasses).attr('for',$element.find(inputSelector).attr('id'));if(icon){$label.html('<i class="icon '+icon+'"></i>')}if(textLabel){$label.html(''+textLabel+'')}$div=$element.find(inputSelector).wrap($('<div>')).parent().data('animated',false);if($element.data('animated')!==false)$div.addClass('switch-animate').data('animated',true);$div.append($switchLeft).append($label).append($switchRight);$element.find('>div').addClass($element.find(inputSelector).is(':checked')?'switch-on':'switch-off');if($element.find(inputSelector).is(':disabled'))$(this).addClass('deactivate');var changeStatus=function($this){if($element.parent('label').is('.label-change-switch')){}else{$this.siblings('label').trigger('mousedown').trigger('mouseup').trigger('click')}};$element.on('keydown',function(e){if(e.keyCode===32){e.stopImmediatePropagation();e.preventDefault();changeStatus($(e.target).find('span:first'))}});$switchLeft.on('click',function(e){changeStatus($(this))});$switchRight.on('click',function(e){changeStatus($(this))});$element.find(inputSelector).on('change',function(e,skipOnChange){var $this=$(this),$element=$this.parent(),thisState=$this.is(':checked'),state=$element.is('.switch-off');e.preventDefault();$element.css('left','');if(state===thisState){if(thisState)$element.removeClass('switch-off').addClass('switch-on');else $element.removeClass('switch-on').addClass('switch-off');if($element.data('animated')!==false)$element.addClass("switch-animate");if(typeof skipOnChange==='boolean'&&skipOnChange)return;$element.parent().trigger('switch-change',{'el':$this,'value':thisState})}});$element.find('label').on('mousedown touchstart',function(e){var $this=$(this);moving=false;e.preventDefault();e.stopImmediatePropagation();$this.closest('div').removeClass('switch-animate');if($this.closest('.has-switch').is('.deactivate')){$this.unbind('click')}else if($this.closest('.switch-on').parent().is('.radio-no-uncheck')){$this.unbind('click')}else{$this.on('mousemove touchmove',function(e){var $element=$(this).closest('.make-switch'),relativeX=(e.pageX||e.originalEvent.targetTouches[0].pageX)-$element.offset().left,percent=(relativeX/$element.width())*100,left=25,right=75;moving=true;if(percent<left)percent=left;else if(percent>right)percent=right;$element.find('>div').css('left',(percent-right)+"%")});$this.on('click touchend',function(e){var $this=$(this),$target=$(e.target),$myRadioCheckBox=$target.siblings('input');e.stopImmediatePropagation();e.preventDefault();$this.unbind('mouseleave');if(moving)$myRadioCheckBox.prop('checked',!(parseInt($this.parent().css('left'))<-25));else $myRadioCheckBox.prop("checked",!$myRadioCheckBox.is(":checked"));moving=false;$myRadioCheckBox.trigger('change')});$this.on('mouseleave',function(e){var $this=$(this),$myInputBox=$this.siblings('input');e.preventDefault();e.stopImmediatePropagation();$this.unbind('mouseleave');$this.trigger('mouseup');$myInputBox.prop('checked',!(parseInt($this.parent().css('left'))<-25)).trigger('change')});$this.on('mouseup',function(e){e.stopImmediatePropagation();e.preventDefault();$(this).unbind('mousemove')})}});if($form.data('bootstrapSwitch')!=='injected'){$form.bind('reset',function(){setTimeout(function(){$form.find('.make-switch').each(function(){var $input=$(this).find(inputSelector);$input.prop('checked',$input.is(':checked')).trigger('change')})},1)});$form.data('bootstrapSwitch','injected')}})},toggleActivation:function(){var $this=$(this);$this.toggleClass('deactivate');$this.find(inputSelector).prop('disabled',$this.is('.deactivate'))},isActive:function(){return!$(this).hasClass('deactivate')},setActive:function(active){var $this=$(this);if(active){$this.removeClass('deactivate');$this.find(inputSelector).removeAttr('disabled')}else{$this.addClass('deactivate');$this.find(inputSelector).attr('disabled','disabled')}},toggleState:function(skipOnChange){var $input=$(this).find(':checkbox');$input.prop('checked',!$input.is(':checked')).trigger('change',skipOnChange)},toggleRadioState:function(skipOnChange){var $radioinput=$(this).find(':radio');$radioinput.not(':checked').prop('checked',!$radioinput.is(':checked')).trigger('change',skipOnChange)},toggleRadioStateAllowUncheck:function(uncheck,skipOnChange){var $radioinput=$(this).find(':radio');if(uncheck){$radioinput.not(':checked').trigger('change',skipOnChange)}else{$radioinput.not(':checked').prop('checked',!$radioinput.is(':checked')).trigger('change',skipOnChange)}},setState:function(value,skipOnChange){$(this).find(inputSelector).prop('checked',value).trigger('change',skipOnChange)},setOnLabel:function(value){var $switchLeft=$(this).find(".switch-left");$switchLeft.html(value)},setOffLabel:function(value){var $switchRight=$(this).find(".switch-right");$switchRight.html(value)},setOnClass:function(value){var $switchLeft=$(this).find(".switch-left");var color='';if(value!==undefined){if($(this).attr('data-on')!==undefined){color="switch-"+$(this).attr('data-on')}$switchLeft.removeClass(color);color="switch-"+value;$switchLeft.addClass(color)}},setOffClass:function(value){var $switchRight=$(this).find(".switch-right");var color='';if(value!==undefined){if($(this).attr('data-off')!==undefined){color="switch-"+$(this).attr('data-off')}$switchRight.removeClass(color);color="switch-"+value;$switchRight.addClass(color)}},setAnimated:function(value){var $element=$(this).find(inputSelector).parent();if(value===undefined)value=false;$element.data('animated',value);$element.attr('data-animated',value);if($element.data('animated')!==false){$element.addClass("switch-animate")}else{$element.removeClass("switch-animate")}},setSizeClass:function(value){var $element=$(this);var $switchLeft=$element.find(".switch-left");var $switchRight=$element.find(".switch-right");var $label=$element.find("label");$.each(['switch-mini','switch-small','switch-large'],function(i,el){if(el!==value){$switchLeft.removeClass(el);$switchRight.removeClass(el);$label.removeClass(el)}else{$switchLeft.addClass(el);$switchRight.addClass(el);$label.addClass(el)}})},status:function(){return $(this).find(inputSelector).is(':checked')},destroy:function(){var $element=$(this),$div=$element.find('div'),$form=$element.closest('form'),$inputbox;$div.find(':not(input)').remove();$inputbox=$div.children();$inputbox.unwrap().unwrap();$inputbox.unbind('change');if($form){$form.unbind('reset');$form.removeData('bootstrapSwitch')}return $inputbox}};if(methods[method])return methods[method].apply(this,Array.prototype.slice.call(arguments,1));else if(typeof method==='object'||!method)return methods.init.apply(this,arguments);else $.error('Method '+method+' does not exist!')}}(jQuery);(function($){$(function(){$('.make-switch')['bootstrapSwitch']()})})(jQuery);



$(function(){
    $('.refresh').click(function(){
        window.location.reload();
    });
    $('#back').click(function(){
      history.go(-1);
    });
});
