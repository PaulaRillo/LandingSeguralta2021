jQuery(window).on("load",function(){jQuery(".avada-select-wrapper").each(function(){var e=jQuery(this).find("select").val(),a=jQuery(this).find('[data-value="'+e+'"]'),t=jQuery(this).find("[data-checked]");a.length&&!a.is("[data-checked]")&&(t.removeAttr("data-checked"),a.attr("data-checked",!0))}),jQuery("body").on("click",".avada-color-select, .avada-image-select, .avada-button-select",function(e){var a=jQuery(this).closest(".avada-select-wrapper"),t=a.find("select"),d=jQuery(this).attr("data-value");if(e.preventDefault(),!jQuery(this).attr("data-disabled")){if(jQuery(this).is("[data-checked]"))return a.find("[data-checked]").removeAttr("data-checked"),void t.val("").trigger("change.wc-variation-form");a.find("[data-checked]").removeAttr("data-checked"),jQuery(this).attr("data-checked",!0),t.val(d).trigger("change.wc-variation-form")}}),jQuery("body").on("DOMNodeInserted DOMNodeRemoved",".avada-select-wrapper select",function(){var e=jQuery(this);jQuery(this).closest("td.value").find(".avada-color-select").each(function(){var a=jQuery(this).attr("data-value");e.find('[value="'+a+'"]').length?jQuery(this).removeAttr("data-disabled"):jQuery(this).attr("data-disabled",!0)})}),jQuery("body").on("click",".reset_variations",function(){jQuery(this).closest(".variations_form").find("[data-checked]").removeAttr("data-checked")})});