jQuery(document).ready(function() {
    jQuery("div[class*='imgwall-cell'] svg").each(function() {
        let aspectratio = jQuery(this).width() / jQuery(this).height();

        let heightrestr = jQuery(this).closest("figure").attr("data-hr");

        jQuery(this).css("max-width", heightrestr * aspectratio);

        jQuery(this).next().css("max-width", heightrestr * aspectratio);
    });

    jQuery("[class*='imgwall-wrapper']").each(function() {
        let id = jQuery(this).attr('data-mid');

        jQuery(this).parent().addClass("imgwall-module-" + id);
    });
});