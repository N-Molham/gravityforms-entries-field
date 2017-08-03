/**
 * Created by Nabeel on 2016-02-02.
 */
!function(a,b,c,d){a(function(){var b=a("#gform_fields"),d=null,e=0;
// when form field changes
b.on("change gfef-change",".gfef_form_setting select",function(b,c){var f=a(this);null===d&&(
// generated forms
d=f.data("forms"),e=d.length),c=c||!1,c&&f.val(f.attr("data-value"));
// fetch which form is selected
for(var g=null,h=parseInt(f.val()),i=0;i<e;i++)if(h===parseInt(d[i].id)){g=d[i];break}if(null===g)
// skip if the form wan't not found
return!0;!1===c&&
// save value
SetFieldProperty("selected_form",h);
// query linked fields dropdown
var j=f.closest("ul").find(".gfef_form_field_setting select").html(function(a){return function(){for(var b=[],c=0;c<a.fields.length;c++)b.push('<option value="'+a.fields[c].id+'">'+a.fields[c].label+"</option>");return b.join("")}}(g)).removeClass("disabled");
// auto-select first option
j.find("option:first").prop("selected",!0),j.trigger("gfef-change"),c&&j.val(j.attr("data-value"))}),
// when form field changes
b.on("change gfef-change",".gfef_form_field_setting select",function(a){
// save value
SetFieldProperty("selected_field",a.currentTarget.value)}),
// when field is opened
a(c).on("gform_load_field_settings",function(b,c){"form_entries"===c.type&&
// trigger change event
setTimeout(function(){var b=a("#field_"+c.id);b.find(".gfef_form_field_setting select").attr("data-value",c.selected_field),b.find(".gfef_form_setting select").attr("data-value",c.selected_form).trigger("gfef-change",[!0])},10)})})}(jQuery,window,document);